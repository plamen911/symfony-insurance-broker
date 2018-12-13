<?php

declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Car;
use AppBundle\Entity\Client;
use AppBundle\Entity\Document;
use AppBundle\Form\CarType;
use AppBundle\Form\ClientType;
use AppBundle\Service\Aws\UploadInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CarController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("car")
 */
class CarController extends Controller
{
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var UploadInterface $uploadService */
    private $uploadService;

    /**
     * PolicyController constructor.
     * @param EntityManagerInterface $em
     * @param UploadInterface $uploadService
     */
    public function __construct(EntityManagerInterface $em, UploadInterface $uploadService)
    {
        $this->em = $em;
        $this->uploadService = $uploadService;
    }

    /**
     * Lists all car entities.
     *
     * @Route("/", name="car_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $cars = $em->getRepository('AppBundle:Car')->findAll();

        return $this->render('car/index.html.twig', array(
            'cars' => $cars,
        ));
    }

    /**
     * Creates a new car entity.
     *
     * @Route("/new", name="car_new")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // upload car documents
            if (null !== $request->files->get('documents')) {
                /** @var UploadedFile $file */
                foreach ($request->files->get('documents') as $file) {
                    $fileUrl = $this->uploadService->upload(
                        $file->getPathname(),
                        $this->uploadService->generateUniqueFileName() . '.' . $file->getClientOriginalExtension(),
                        $file->getClientMimeType()
                    );

                    $document = new Document();
                    $document->setFileUrl($fileUrl);
                    $document->setFileName($file->getClientOriginalName());
                    $document->setMimeType($file->getClientMimeType());
                    $car->addDocument($document);
                }
            }

            $this->em->persist($car);
            $this->em->flush();

            $this->addFlash('success', 'МПС бе успешно създадено.');

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('car/new.html.twig', [
            'car' => $car,
            'form' => $form->createView()
        ]);
    }

    /**
     * Finds and displays a car entity.
     *
     * @Route("/{id}", name="car_show")
     * @Method("GET")
     * @param Car $car
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Car $car)
    {
        $deleteForm = $this->createDeleteForm($car);

        return $this->render('car/show.html.twig', array(
            'car' => $car,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing car entity.
     *
     * @Route("/{id}/edit", name="car_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     * @param Request $request
     * @param Car $car
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Car $car)
    {
        $deleteForm = $this->createDeleteForm($car);
        $editForm = $this->createForm('AppBundle\Form\CarType', $car);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            // upload car documents
            if (null !== $request->files->get('documents')) {
                /** @var UploadedFile $file */
                foreach ($request->files->get('documents') as $file) {
                    $fileUrl = $this->uploadService->upload(
                        $file->getPathname(),
                        $this->uploadService->generateUniqueFileName() . '.' . $file->getClientOriginalExtension(),
                        $file->getClientMimeType()
                    );

                    $document = new Document();
                    $document->setFileUrl($fileUrl);
                    $document->setFileName($file->getClientOriginalName());
                    $document->setMimeType($file->getClientMimeType());
                    $car->addDocument($document);
                }
            }

            $car->setUpdatedAt(new \DateTime());
            $this->em->flush();

            $this->addFlash('success', 'Данните бяха успешно записани.');

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView()
        ]);
    }

    /**
     * Deletes a car entity.
     *
     * @Route("/{id}", name="car_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Car $car)
    {
        $form = $this->createDeleteForm($car);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($car);
            $em->flush();
        }

        return $this->redirectToRoute('car_index');
    }

    /**
     * @Route("/{car}/document/{document}/delete", name="car_document_delete", methods={"DELETE"}, requirements={"car": "\d+", "document": "\d+"})
     * @param Car $car
     * @param Document $document
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteDocument(Car $car, Document $document)
    {
        try {
            $this->uploadService->delete(basename($document->getFileUrl()));
            $this->em->remove($document);
            $this->em->flush();
            $this->addFlash('success', 'Документът бе успешно изтрит.');

        } catch (Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }

        return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
    }

    /**
     * @Route("/{car}/owner/new", name="car_new_owner", methods={"GET","POST"}, requirements={"car": "\d+"})
     * @param Request $request
     * @param Car $car
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newOwnerAction(Request $request, Car $car)
    {
        $refUrl = $request->query->get('ref');

        $owner = new Client();
        $form = $this->createForm(ClientType::class, $owner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $car->setOwner($owner);
            $this->em->persist($owner);
            $this->em->flush();

            $this->addFlash('success', 'Собственикът на МПС бе успешно добавен.');

            if (!empty($refUrl)) {
                return $this->redirect($refUrl);
            }

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('car/new-owner.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'refUrl' => $refUrl
        ]);
    }

    /**
     * Creates a form to delete a car entity.
     *
     * @param Car $car The car entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Car $car)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('car_delete', array('id' => $car->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
