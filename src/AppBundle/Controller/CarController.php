<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Car;
use AppBundle\Entity\Client;
use AppBundle\Entity\Document;
use AppBundle\Form\CarType;
use AppBundle\Form\ClientType;
use AppBundle\Service\Car\CarServiceInterface;
use AppBundle\Service\Client\ClientServiceInterface;
use AppBundle\Service\Document\DocumentServiceInterface;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;
//
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;

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
    use DataTablesTrait;

    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var CarServiceInterface $carService */
    private $carService;

    /** @var ClientServiceInterface $clientService */
    private $clientService;

    /** @var DocumentServiceInterface $documentService */
    private $documentService;

    /**
     * CarController constructor.
     *
     * @param FormErrorServiceInterface $formErrorsService
     * @param CarServiceInterface $carService
     * @param ClientServiceInterface $clientService
     * @param DocumentServiceInterface $documentService
     */
    public function __construct(FormErrorServiceInterface $formErrorsService, CarServiceInterface $carService, ClientServiceInterface $clientService, DocumentServiceInterface $documentService)
    {
        $this->formErrorService = $formErrorsService;
        $this->carService = $carService;
        $this->clientService = $clientService;
        $this->documentService = $documentService;
    }

    /**
     * Lists all car entities.
     *
     * @Route("/", methods={"GET", "POST"}, name="car_index")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
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
            ->add('idNumber', TextColumn::class, ['label' => 'Рег. No'])
            ->add('carMake', TextColumn::class, [
                'label' => 'МПС',
                'render' => function ($value, $car) {
                    /** @var Car $car */
                    return $car->getCarMake() . ' ' . $car->getCarModel();
                }
            ])
            ->add('carModel', TextColumn::class, [
                'className' => 'd-none',
                'visible' => false
            ])
            ->add('ownerFirstName', TextColumn::class, [
                'field' => 'owner.firstName',
                'label' => 'Собственик',
                'render' => function ($value, $car) {
                    /** @var Car $car */
                    return $car->getOwner()->getFullName();
                }
            ])
            ->add('ownerMiddleName', TextColumn::class, [
                'field' => 'owner.middleName',
                'className' => 'd-none',
                'visible' => false
            ])
            ->add('ownerLastName', TextColumn::class, [
                'field' => 'owner.lastName',
                'className' => 'd-none',
                'visible' => false
            ])
            ->add('buttons', TextColumn::class, [
                'label' => '',
                'searchable' => false,
                'className' => 'text-center',
                'render' => function($value, $car) {
                    /** @var Car $car */
                    return '<a href="' . $this->generateUrl('car_edit', ['id' => $car->getId()]) . '" class="btn btn-sm btn-secondary" title="Редактирай"><i class="fas fa-edit"></i></a>';
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Car::class,
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('car/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * Creates a new car entity.
     *
     * @Route("/new", methods={"GET", "POST"}, name="car_new")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function newAction(Request $request)
    {
        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->carService->newCar($request, $car);
            $this->addFlash('success', 'МПС бе успешно създадено.');

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('car/new.html.twig', [
            'car' => $car,
            'form' => $form->createView()
        ]);
    }

    /**
     * Displays a form to edit an existing car entity.
     *
     * @Route("/{id}/edit", name="car_edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Car $car
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function editAction(Request $request, Car $car)
    {
        $refUrl = $request->getRequestUri();
        if (null === $car->getOwner()) {
            $this->addFlash('warning', 'Моля, изберете или въведете собственик на МПС.');
            return $this->redirectToRoute('car_new_owner', ['car' => $car->getId(), 'type' => 'owner', 'ref' => $refUrl]);
        }

        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->carService->editCar($request, $car);
            $this->addFlash('success', 'Данните за МПС бяха успешно записани.');

            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        return $this->render('car/edit.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'refUrl' => $refUrl,
            'canDelete' => $this->carService->canDelete($car)
        ]);
    }

    /**
     * Deletes a car entity.
     *
     * @Route("/{car}", methods={"DELETE"}, name="car_delete", requirements={"car": "\d+"})
     * @param Car $car
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Car $car)
    {
        if (!$this->carService->canDelete($car)) {
            $this->addFlash('danger', 'Нямате права за тази операция.');
            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        if (0 < $cnt = $car->getPolicies()->count()) {
            $this->addFlash('danger', 'Не може да изтриете това МПС, защото принадлежи към ' . $cnt . ' бр. полици.');
            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }

        try {
            $this->carService->deleteCar($car);
            $this->addFlash('success', 'МПС бе успешно изтрито.');

            return $this->redirectToRoute('car_index');

        } catch (Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
            return $this->redirectToRoute('car_edit', ['id' => $car->getId()]);
        }
    }

    /**
     * @Route("/{car}/document/{document}/delete", name="car_document_delete", methods={"DELETE"}, requirements={"car": "\d+", "document": "\d+"})
     * @param Request $request
     * @param Car $car
     * @param Document $document
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteDocument(Request $request, Car $car, Document $document)
    {
        try {
            $this->documentService->deleteDocument($document);
            $this->addFlash('success', 'Документът бе успешно изтрит.');

        } catch (Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }

        if (null !== $refUrl = $request->query->get('ref')) {
            return $this->redirect($refUrl);
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
            $this->clientService->editClient($representative);
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

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            if ('owner' === $type) {
                $message = 'Собственикът на МПС бе успешно добавен.';
                $car->setOwner($client);
            } else {
                $message = 'Пълномощникът бе успешно добавен.';
                $car->setRepresentative($client);
            }
            $this->clientService->newClient($client);

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

            $this->clientService->newClient($client);

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
     * @Route("/owner/search", name="car_search_owner", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchOwner(Request $request)
    {
        $q = $request->query->get('term');
        /** @var Client[]|null $owners */
        $owners = $this->clientService->findByKeyword((string)$q);

        $data = [];
        if ($owners) {
            foreach ($owners as $owner) {
                $data[] = [
                    'value' => $owner->getId(),
                    'label' => $owner->getIdNumber() . ': ' . $owner->getFullName()
                ];
            }
        }

        return $this->json($data);
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
