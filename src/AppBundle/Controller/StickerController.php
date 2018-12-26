<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Sticker;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use AppBundle\Service\Sticker\StickerServiceInterface;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
//
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;

/**
 * Class StickerController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("sticker")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class StickerController extends Controller
{
    use DataTablesTrait;

    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var StickerServiceInterface $stickerService */
    private $stickerService;

    /**
     * StickerController constructor.
     * @param FormErrorServiceInterface $formErrorService
     * @param StickerServiceInterface $stickerService
     */
    public function __construct(FormErrorServiceInterface $formErrorService, StickerServiceInterface $stickerService)
    {
        $this->formErrorService = $formErrorService;
        $this->stickerService = $stickerService;
    }

    /**
     * Lists all sticker entities.
     *
     * @Route("/", methods={"GET", "POST"}, name="sticker_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // https://omines.github.io/datatables-bundle/
        $table = $this->createDataTable([
            'stateSave' => true,
            'pageLength' => 25,
            'autoWidth' => true,
            'searching' => true,
        ])
            ->add('idNumber', TextColumn::class, ['label' => 'Стикер No'])
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
                'render' => function($value, $sticker) {
                    /** @var Sticker $sticker */
                    if (null === $sticker->getPolicy()) {
                        return '--';
                    }
                    return '<a href="' . $this->generateUrl('policy_edit', ['id' => $sticker->getPolicy()->getId()]) . '"' .
                        ' class="btn btn-sm btn-link text-dark" title="Виж" target="_blank"><i class="fas fa-external-link-alt"></i></a>';
                }
            ])
            ->add('createdAt', DateTimeColumn::class, [
                'searchable' => false,
                'format' => 'd.m.Y H:i',
                'label' => 'Добавен на',
                'render' => function($value, $sticker) {
                    /** @var Sticker $sticker */
                    return '<small>' . $sticker->getCreatedAt()->format('m/d/Y H:i') . '</small>';
                }
            ])
            ->add('createdBy', TextColumn::class, [
                'field' => 's.author',
                'label' => 'Добавен от',
                'render' => function($value, $sticker) {
                    /** @var Sticker $sticker */
                    return $sticker->getAuthor()->getFullName();
                }
            ])
            ->add('buttons', TextColumn::class, [
                'label' => '',
                'searchable' => false,
                'className' => 'text-center',
                'render' => function($value, $sticker) {
                    /** @var Sticker $sticker */
                    return '<a href="' . $this->generateUrl('sticker_edit', ['id' => $sticker->getId()]) . '" class="btn btn-sm btn-secondary" title="Редактирай"><i class="fas fa-edit"></i></a>';
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Sticker::class,
                'query' => function (QueryBuilder $builder) {
                    $builder
                        ->select('s')
                        ->addSelect('insurer')
                        ->addSelect('agent')
                        ->addSelect('policy')
                        ->addSelect('author')
                        ->from(Sticker::class, 's')
                        ->leftJoin('s.insurer', 'insurer')
                        ->leftJoin('s.agent', 'agent')
                        ->leftJoin('s.policy', 'policy')
                        ->leftJoin('s.author', 'author');
                }
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('sticker/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * Creates a new sticker entity.
     *
     * @Route("/new", methods={"GET", "POST"}, name="sticker_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newAction(Request $request)
    {
        $sticker = new Sticker();
        $form = $this->createForm('AppBundle\Form\StickerType', $sticker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($sticker);
            $em->flush();

            return $this->redirectToRoute('sticker_show', array('id' => $sticker->getId()));
        }

        return $this->render('sticker/new.html.twig', array(
            'sticker' => $sticker,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a sticker entity.
     *
     * @Route("/{id}", methods={"GET"} name="sticker_show")
     */
    public function showAction(Sticker $sticker)
    {
        $deleteForm = $this->createDeleteForm($sticker);

        return $this->render('sticker/show.html.twig', array(
            'sticker' => $sticker,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing sticker entity.
     *
     * @Route("/{id}/edit", name="sticker_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Sticker $sticker)
    {
        $deleteForm = $this->createDeleteForm($sticker);
        $editForm = $this->createForm('AppBundle\Form\StickerType', $sticker);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sticker_edit', array('id' => $sticker->getId()));
        }

        return $this->render('sticker/edit.html.twig', array(
            'sticker' => $sticker,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a sticker entity.
     *
     * @Route("/{id}", methods={"DELETE"}, name="sticker_delete")
     */
    public function deleteAction(Request $request, Sticker $sticker)
    {
        $form = $this->createDeleteForm($sticker);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($sticker);
            $em->flush();
        }

        return $this->redirectToRoute('sticker_index');
    }

    /**
     * Creates a form to delete a sticker entity.
     *
     * @param Sticker $sticker The sticker entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Sticker $sticker)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('sticker_delete', array('id' => $sticker->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
