<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\GreenCard;
use AppBundle\Form\GreenCardFormType;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use AppBundle\Service\GreenCard\GreenCardServiceInterface;
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
 * Class GreenCardController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("green-card")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class GreenCardController extends Controller
{
    use DataTablesTrait;

    const PER_PAGE = 25;

    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var GreenCardServiceInterface $greenCardService */
    private $greenCardService;

    /**
     * GreenCardController constructor.
     * @param FormErrorServiceInterface $formErrorService
     * @param GreenCardServiceInterface $greenCardService
     */
    public function __construct(FormErrorServiceInterface $formErrorService, GreenCardServiceInterface $greenCardService)
    {
        $this->formErrorService = $formErrorService;
        $this->greenCardService = $greenCardService;
    }

    /**
     * Lists all green card entities.
     *
     * @Route("/", methods={"GET", "POST"}, name="green_card_index")
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
                'label' => 'Зелена карта No'
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
                'render' => function($value, $greenCard) {
                    /** @var GreenCard $greenCard */
                    if (null === $greenCard->getPolicy()) {
                        return '--';
                    }
                    return '<a href="' . $this->generateUrl('policy_edit', ['id' => $greenCard->getPolicy()->getId()]) . '"' .
                        ' class="btn btn-sm btn-link text-dark" title="Виж" target="_blank"><i class="fas fa-external-link-alt"></i></a>';
                }
            ])
            ->add('createdAt', DateTimeColumn::class, [
                'searchable' => false,
                'format' => 'd.m.Y H:i',
                'label' => 'Добавена на',
                'render' => function($value, $greenCard) {
                    /** @var GreenCard $greenCard */
                    return '<small>' . $greenCard->getCreatedAt()->format('m/d/Y H:i') . '</small>';
                }
            ])
            ->add('createdBy', TextColumn::class, [
                'field' => 'author.fullName',
                'label' => 'Добавена от',
                'render' => function($value, $greenCard) {
                    /** @var GreenCard $greenCard */
                    return $greenCard->getAuthor()->getFullName();
                }
            ])
            ->add('buttons', TextColumn::class, [
                'label' => '',
                'searchable' => false,
                'className' => 'text-center',
                'render' => function($value, $greenCard) {
                    /** @var GreenCard $greenCard */
                    return '<a href="' . $this->generateUrl('green_card_edit', ['id' => $greenCard->getId()]) . '" class="btn btn-sm btn-secondary" title="Редактирай"><i class="fas fa-edit"></i></a>';
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => GreenCard::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('g')
                        ->addSelect('insurer')
                        ->addSelect('agent')
                        ->addSelect('policy')
                        ->addSelect('author')
                        ->from(GreenCard::class, 'g')
                        ->leftJoin('g.insurer', 'insurer')
                        ->leftJoin('g.agent', 'agent')
                        ->leftJoin('g.policy', 'policy')
                        ->leftJoin('g.author', 'author');
                }
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('green-card/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * Creates a new green card entity.
     *
     * @Route("/new", methods={"GET", "POST"}, name="green_card_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newAction(Request $request)
    {
        $greenCard = new GreenCard();
        $form = $this->createForm(GreenCardFormType::class, $greenCard);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->greenCardService->newGreenCard($greenCard);
            $this->addFlash('success', 'Зелената карта бе успешно въведена.');

            return $this->redirectToRoute('green_card_edit', ['id' => $greenCard->getId()]);
        }

        return $this->render('green-card/new.html.twig', [
            'greenCard' => $greenCard,
            'form' => $form->createView()
        ]);
    }

    /**
     * Displays a form to edit an existing green card entity.
     *
     * @Route("/{id}/edit", methods={"GET", "POST"}, name="green_card_edit", requirements={"id": "\d+"})
     * @param Request $request
     * @param GreenCard $greenCard
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, GreenCard $greenCard)
    {
        $form = $this->createForm(GreenCardFormType::class, $greenCard);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->greenCardService->editGreenCard($greenCard);
            $this->addFlash('success', 'Данните за зелената карта бяха успешно записани.');

            return $this->redirectToRoute('green_card_edit', ['id' => $greenCard->getId()]);
        }

        return $this->render('green-card/edit.html.twig', [
            'greenCard' => $greenCard,
            'form' => $form->createView()
        ]);
    }

    /**
     * Deletes a green card entity.
     *
     * @Route("/{id}", methods={"DELETE"}, name="green_card_delete", requirements={"id": "\d+"})
     * @param GreenCard $greenCard
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(GreenCard $greenCard)
    {
        $this->greenCardService->deleteGreenCard($greenCard);
        $this->addFlash('success', 'Зелената карта бе успешно изтрита.');

        return $this->redirectToRoute('green_card_index');
    }
}
