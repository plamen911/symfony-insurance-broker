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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
use Symfony\Component\Routing\Matcher\UrlMatcher;

/**
 * Class CarController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("car")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
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
        $refUrl = $request->getRequestUri();
        if (null === $car->getOwner()) {
            $this->addFlash('warning', 'Моля, изберете или въведете собственик на МПС.');
            return $this->redirectToRoute('car_new_owner', ['car' => $car->getId(), 'type' => 'owner', 'ref' => $refUrl]);
        }

        $deleteForm = $this->createDeleteForm($car);
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

            $car->setUpdatedAt(new \DateTime());
            $this->em->flush();

            $this->addFlash('success', 'Данните за МПС бяха успешно записани.');

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
            'refUrl' => $refUrl
        ]);
    }

    /**
     * Deletes a car entity.
     *
     * @Route("/{id}", name="car_delete")
     * @Method("DELETE")
     * @param Request $request
     * @param Car $car
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
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
     * @Route("/{car}/representative/{representative}/delete", name="car_representative_delete", methods={"DELETE"}, requirements={"car": "\d+", "representative": "\d+"})
     * @param Car $car
     * @param Client $representative
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteRepresentativeAction(Car $car, Client $representative)
    {
        try {
            $representative->removeRepresentativeCar($car);
            $this->em->flush();
            $this->addFlash('success', 'Пълномощникът бе успешно премахнат.');

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
        $type = (in_array($type = $request->query->get('type'), ['owner', 'representative'])) ? $type : 'owner';
        $refUrl = $request->query->get('ref');

        $autoCompleteForm = $this->createAutoCompleteForm();
        $autoCompleteForm->handleRequest($request);

        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ('owner' === $type) {
                $message = 'Собственикът на МПС бе успешно добавен.';
                $car->setOwner($client);
            } else {
                $message = 'Пълномощникът бе успешно добавен.';
                $car->setRepresentative($client);
            }
            $this->em->persist($client);
            $this->em->flush();

            $this->addFlash('success', $message);
            if (!empty($refUrl)) {
                return $this->redirect($refUrl);
            }

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        if ($autoCompleteForm->isSubmitted() && $autoCompleteForm->isValid()) {
            if (null === $client = $autoCompleteForm['owner']->getData()) {
                $this->addFlash('danger', 'Невалиден ' . ('owner' === $type ? 'собственик' : 'пълномощник') . '!');
                return $this->redirectToRoute('car_new_owner', ['car' => $car->getId(), 'type' => $type, 'ref' => $refUrl]);
            }

            if ('owner' === $type) {
                $message = 'Собственикът на МПС бе успешно променен.';
                $car->setOwner($client);
            } else {
                $message = 'Пълномощникът бе успешно променен.';
                $car->setRepresentative($client);
            }

            $this->em->persist($client);
            $this->em->flush();

            $this->addFlash('success', $message);
            if (!empty($refUrl)) {
                return $this->redirect($refUrl);
            }

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('car/new-owner.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'form_autocomplete' => $autoCompleteForm->createView(),
            'type' => $type,
            'refUrl' => $refUrl
        ]);
    }

    /**
     * @Route("/{car}/owner/search", name="car_search_owner", methods={"GET"}, requirements={"car": "\d+"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchOwner(Request $request)
    {
        $q = $request->query->get('term'); // use "term" instead of "q" for jquery-ui
        /** @var Client[]|null $owners */
        $owners = $this->em->getRepository(Client::class)->findByKeyword((string)$q);

        $data = [];
        if ($owners) {
            foreach ($owners as $owner) {
                $data[] = [
                    //'id' => $owner->getId(),
                    'value' => $owner->getId(),
                    'label' => $owner->getIdNumber() . ': ' . $owner->getFullName()
                ];
            }
        }

        return $this->json($data);
    }

    /**
     * @Route("/{car}/owner/get", name="car_get_owner", methods={"GET"}, requirements={"car": "\d+"})
     * @param null|int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getOwner(?int $id = null)
    {
        /** @var Client $owner */
        $owner = $this->em->getRepository(Client::class)->find($id);

        return $this->json($owner->getIdNumber() . ': ' . $owner->getFullName());
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
            ->getForm();
    }

    /**
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createAutoCompleteForm()
    {
        return $this->createFormBuilder()
            ->setMethod('POST')
            ->add('owner', AutocompleteType::class, [
                'class' => Client::class,
                'label' => 'Изберете съществуващ собственик',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'placeholder' => 'Въведи ЕГН, име, презиме или фамилия'
                ]
            ])
            ->getForm();
    }
}
