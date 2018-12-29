<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Bill;
use AppBundle\Form\BillFormType;
use AppBundle\Service\Bill\BillServiceInterface;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
//
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;

/**
 * Class BillController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("bill")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class BillController extends Controller
{
    use DataTablesTrait;

    const PER_PAGE = 25;

    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var BillServiceInterface $billService */
    private $billService;

    /**
     * BillController constructor.
     * @param FormErrorServiceInterface $formErrorService
     * @param BillServiceInterface $billService
     */
    public function __construct(FormErrorServiceInterface $formErrorService, BillServiceInterface $billService)
    {
        $this->formErrorService = $formErrorService;
        $this->billService = $billService;
    }

    /**
     * Lists all bill entities.
     *
     * @Route("/", methods={"GET", "POST"}, name="bill_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // https://omines.github.io/datatables-bundle/
        $table = $this->createDataTable([
            'stateSave' => true,
            'pageLength' => self::PER_PAGE,
            'autoWidth' => true,
            'searching' => true,
        ])
            ->add('idNumber', TextColumn::class, [
                'label' => 'Сметка No'
            ])
            ->add('insurerName', TextColumn::class, [
                'field' => 'insurer.name',
                'label' => 'Застраховател'
            ])
            ->add('receivedAt', DateTimeColumn::class, [
                'searchable' => false, // Important - make datetime col. non-searchable!
                'format' => 'd.m.Y',
                'label' => 'Получена на'
            ])
            ->add('agentName', TextColumn::class, [
                'field' => 'agent.fullName',
                'label' => 'Агент'
            ])
            ->add('policyIdNumber', TextColumn::class, [
                'field' => 'policy.idNumber',
                'label' => 'Полица No',
                'className' => 'text-center',
                'render' => function($value, $bill) {
                    /** @var Bill $bill */
                    if (null === $bill->getPolicy()) {
                        return '--';
                    }
                    return '<a href="' . $this->generateUrl('policy_edit', ['id' => $bill->getPolicy()->getId()]) . '"' .
                        ' class="btn btn-sm btn-link text-dark" title="Виж" target="_blank"><i class="fas fa-external-link-alt"></i></a>';
                }
            ])
            ->add('createdAt', DateTimeColumn::class, [
                'searchable' => false,
                'format' => 'd.m.Y H:i',
                'label' => 'Добавена на',
                'render' => function($value, $bill) {
                    /** @var Bill $bill */
                    return '<small>' . $bill->getCreatedAt()->format('m/d/Y H:i') . '</small>';
                }
            ])
            ->add('createdBy', TextColumn::class, [
                'field' => 'author.fullName',
                'label' => 'Добавена от',
                'render' => function($value, $bill) {
                    /** @var Bill $bill */
                    return $bill->getAuthor()->getFullName();
                }
            ])
            ->add('buttons', TextColumn::class, [
                'label' => '',
                'searchable' => false,
                'className' => 'text-center',
                'render' => function($value, $bill) {
                    /** @var Bill $bill */
                    return '<a href="' . $this->generateUrl('bill_edit', ['id' => $bill->getId()]) . '" class="btn btn-sm btn-secondary" title="Редактирай"><i class="fas fa-edit"></i></a>';
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Bill::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('b')
                        ->addSelect('insurer')
                        ->addSelect('agent')
                        ->addSelect('policy')
                        ->addSelect('author')
                        ->from(Bill::class, 'b')
                        ->leftJoin('b.insurer', 'insurer')
                        ->leftJoin('b.agent', 'agent')
                        ->leftJoin('b.policy', 'policy')
                        ->leftJoin('b.author', 'author');
                }
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('bill/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * Creates a new bill entity.
     *
     * @Route("/new", methods={"GET", "POST"}, name="bill_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newAction(Request $request)
    {
        $bill = new Bill();
        $form = $this->createForm(BillFormType::class, $bill);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->billService->newBill($bill);
            $this->addFlash('success', 'Сметката бе успешно въведена.');

            return $this->redirectToRoute('bill_edit', ['id' => $bill->getId()]);
        }

        return $this->render('bill/new.html.twig', [
            'bill' => $bill,
            'form' => $form->createView()
        ]);
    }

    /**
     * Displays a form to edit an existing bill entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="bill_edit", requirements={"id": "\d+"})
     * @param Request $request
     * @param Bill $bill
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Bill $bill)
    {
        $form = $this->createForm(BillFormType::class, $bill);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->billService->editBill($bill);
            $this->addFlash('success', 'Данните за сметката бяха успешно записани.');

            return $this->redirectToRoute('bill_edit', ['id' => $bill->getId()]);
        }

        return $this->render('bill/edit.html.twig', [
            'bill' => $bill,
            'form' => $form->createView()
        ]);
    }

    /**
     * Deletes a bill entity.
     *
     * @Route("/{id}", methods={"DELETE"}, name="bill_delete", requirements={"id": "\d+"})
     * @param Bill $bill
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Bill $bill)
    {
        $this->billService->deleteBill($bill);
        $this->addFlash('success', 'Сметката бе успешно изтрита.');

        return $this->redirectToRoute('bill_index');
    }
}
