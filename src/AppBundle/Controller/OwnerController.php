<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Form\ClientType;
use AppBundle\Service\Client\ClientServiceInterface;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Client controller.
 * Class OwnerController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("owner")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class OwnerController extends Controller
{
    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var ClientServiceInterface $clientService */
    private $clientService;

    /**
     * OwnerController constructor.
     * @param FormErrorServiceInterface $formErrorsService
     * @param ClientServiceInterface $clientService
     */
    public function __construct(FormErrorServiceInterface $formErrorsService, ClientServiceInterface $clientService)
    {
        $this->formErrorService = $formErrorsService;
        $this->clientService = $clientService;
    }

    /**
     * Lists all client entities.
     *
     * @Route("/", name="owner_index", methods={"GET"})
     */
    public function indexAction()
    {
        return $this->render('owner/index.html.twig', [
            'clients' => $this->clientService->findAll()
        ]);
    }

    /**
     * Creates a new client entity.
     *
     * @Route("/new", name="owner_new", methods={"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $owner = new Client();
        $form = $this->createForm(ClientType::class, $owner);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->clientService->newClient($owner);
            $this->addFlash('success', 'Собственикът на МПС бе успешно добавен.');

            return $this->redirectToRoute('owner_show', ['id' => $owner->getId()]);
        }

        return $this->render('owner/new.html.twig', [
            'owner' => $owner,
            'form' => $form->createView(),
            'isNew' => true
        ]);
    }

    /**
     * Finds and displays a client entity.
     *
     * @Route("/{id}", name="owner_show", methods={"GET"})
     * @param Client $owner
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Client $owner)
    {
        $deleteForm = $this->createDeleteForm($owner);

        return $this->render('owner/show.html.twig', array(
            'owner' => $owner,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing client entity.
     *
     * @Route("/{id}/edit", name="owner_edit", methods={"GET", "POST"})
     * @param Request $request
     * @param Client $owner
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function editAction(Request $request, Client $owner)
    {
        $deleteForm = $this->createDeleteForm($owner);
        $form = $this->createForm(ClientType::class, $owner);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->clientService->editClient($owner);
            $this->addFlash('success', 'Данните бяха успешно записани.');

            return $this->redirectToRoute('owner_edit', ['id' => $owner->getId()]);
        }

        return $this->render('owner/edit.html.twig', [
            'owner' => $owner,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'isNew' => false
        ]);
    }

    /**
     * Deletes a client entity.
     *
     * @Route("/{id}", name="owner_delete", methods={"DELETE"})
     * @param Request $request
     * @param Client $client
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Client $client)
    {
        $form = $this->createDeleteForm($client);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->clientService->deleteClient($client);
        }

        return $this->redirectToRoute('owner_index');
    }

    /**
     * Creates a form to delete a client entity.
     *
     * @param Client $client The client entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Client $client)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('owner_delete', array('id' => $client->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
