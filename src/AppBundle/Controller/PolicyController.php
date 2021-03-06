<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Car;
use AppBundle\Entity\Document;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Policy;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Form\CarType;
use AppBundle\Form\PolicyType;
use AppBundle\Service\Aws\UploadInterface;
use AppBundle\Service\Car\CarServiceInterface;
use AppBundle\Service\Document\DocumentServiceInterface;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use AppBundle\Service\Policy\PolicyServiceInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use Omines\DataTablesBundle\Column\DateTimeColumn;
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
 * Class PolicyController
 *
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("policy")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class PolicyController extends Controller
{
    use DataTablesTrait;

    /** @var UploadInterface $uploadService */
    private $uploadService;

    /** @var PolicyServiceInterface $policyService */
    private $policyService;

    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var DocumentServiceInterface $documentService */
    private $documentService;

    /** @var CarServiceInterface $carService */
    private $carService;

    /**
     * PolicyController constructor.
     *
     * @param UploadInterface $uploadService
     * @param FormErrorServiceInterface $formErrorsService
     * @param PolicyServiceInterface $policyService
     * @param DocumentServiceInterface $documentService
     * @param CarServiceInterface $carService
     */
    public function __construct(UploadInterface $uploadService, FormErrorServiceInterface $formErrorsService, PolicyServiceInterface $policyService, DocumentServiceInterface $documentService, CarServiceInterface $carService)
    {
        $this->uploadService = $uploadService;
        $this->formErrorService = $formErrorsService;
        $this->policyService = $policyService;
        $this->documentService = $documentService;
        $this->carService = $carService;
    }

    /**
     * @Route("/", name="policy_index", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToListAction()
    {
        $typeOfPolicy = $this->policyService->getDefaultTypeOfPolicy();

        return $this->redirectToRoute('policy_list', ['typeOfPolicy' => $typeOfPolicy->getId()]);
    }

    /**
     * Lists all policy entities.
     *
     * @Route("/{typeOfPolicy}", name="policy_list", methods={"GET", "POST"}, requirements={"typeOfPolicy": "\d+"})
     * @param Request $request
     * @param TypeOfPolicy $typeOfPolicy
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, TypeOfPolicy $typeOfPolicy)
    {
        // https://omines.github.io/datatables-bundle/
        $table = $this->createDataTable([
            'stateSave' => true,
            'pageLength' => 25,
            'autoWidth' => true,
            'searching' => true,
        ])
            ->add('idNumber', TextColumn::class, ['label' => 'Полица No'])
            ->add('issuedAt', DateTimeColumn::class, [
                'searchable' => false, // Important - make datetime col. non-searchable!
                'format' => 'd.m.Y',
                'label' => 'Издадена на'
            ])
            ->add('insurerName', TextColumn::class, [
                'field' => 'insurer.name',
                'label' => 'Застраховател'
            ])
            ->add('agentName', TextColumn::class, [
                'field' => 'agent.fullName',
                'label' => 'Агент'
            ])
            ->add('total', TextColumn::class, ['label' => 'Дължимо'])
            ->add('paid', TextColumn::class, ['label' => 'Платено'])
            ->add('startsAt', DateTimeColumn::class, [
                'searchable' => false,
                'format' => 'd.m.Y',
                'label' => 'Валидна от',
                'visible' => false
            ])
            ->add('expiresAt', DateTimeColumn::class, [
                'searchable' => false,
                'format' => 'd.m.Y',
                'label' => 'Изтича на',
                'visible' => false
            ])
            ->add('carMake', TextColumn::class, [
                'field' => 'car.carMake',
                'label' => 'МПС',
                'render' => function ($value, $policy) {
                    /** @var Policy $policy */
                    return $policy->getCar()->getCarMake() . ' ' . $policy->getCar()->getCarModel();
                }
            ])
            ->add('carModel', TextColumn::class, [
                'field' => 'car.carModel',
                'className' => 'd-none',
                'visible' => false
            ])
            ->add('carIdNumber', TextColumn::class, [
                'field' => 'car.idNumber',
                'label' => 'Рег. No',
            ])
            ->add('buttons', TextColumn::class, [
                'label' => '',
                'searchable' => false,
                'className' => 'text-center',
                'render' => function($value, $policy) {
                    /** @var Policy $policy */
                    return '<a href="' . $this->generateUrl('policy_edit', ['id' => $policy->getId()]) . '"' .
                        ' class="btn btn-sm btn-secondary" title="Редактирай"><i class="fas fa-edit"></i></a>';
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => Policy::class,
                'query' => function (QueryBuilder $builder) use ($typeOfPolicy) {
                    $builder
                        ->select('p')
                        ->addSelect('car')
                        ->addSelect('insurer')
                        ->addSelect('agent')
                        ->from(Policy::class, 'p')
                        ->leftJoin('p.insurer', 'insurer')
                        ->leftJoin('p.car', 'car')
                        ->leftJoin('p.agent', 'agent')
                        ->where('p.policyType = :policyType')
                        ->setParameter('policyType', $typeOfPolicy->getId())
                    ;
                },
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('policy/index.html.twig', [
            'datatable' => $table,
            'typeOfPolicy' => $typeOfPolicy
        ]);
    }

    /**
     * @Route("/new/type/{typeOfPolicy}", name="policy_new_car", methods={"GET", "POST"}, requirements={"typeOfPolicy": "\d+"})
     * @param Request $request
     * @param TypeOfPolicy $typeOfPolicy
     * @param CarServiceInterface $carService
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function newCarAction(Request $request, TypeOfPolicy $typeOfPolicy, CarServiceInterface $carService)
    {
        $refUrl = $request->query->get('ref');

        $autoCompleteForm = $this->createAutoCompleteForm();
        $autoCompleteForm->handleRequest($request);

        $car = new Car();
        $form = $this->createForm(CarType::class, $car);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $carService->newCar($request, $car);
            $this->addFlash('success', 'МПС бе успешно създадено.');

            return $this->redirectToRoute('policy_new', ['typeOfPolicy' => $typeOfPolicy->getId(), 'car' => $car->getId()]);
        }

        if ($autoCompleteForm->isSubmitted() && $autoCompleteForm->isValid()) {
            /** @var Car $car */
            if (null === $car = $autoCompleteForm['car']->getData()) {
                $this->addFlash('danger', 'Невалидно МПС!');

                return $this->redirectToRoute('policy_new_car', ['typeOfPolicy' => $typeOfPolicy->getId(), 'ref' => $refUrl]);
            }

            return $this->redirectToRoute('policy_new', ['typeOfPolicy' => $typeOfPolicy->getId(), 'car' => $car->getId()]);
        }

        return $this->render('policy/new-car.html.twig', [
            'car' => $car,
            'form' => $form->createView(),
            'form_autocomplete' => $autoCompleteForm->createView(),
            'policyType' => $typeOfPolicy,
            'refUrl' => $refUrl
        ]);
    }

    /**
     * Creates a new policy entity.
     *
     * @Route("/new/type/{typeOfPolicy}/car/{car}", name="policy_new", methods={"GET", "POST"}, requirements={"typeOfPolicy": "\d+", "car": "\d+"})
     * @param Request $request
     * @param TypeOfPolicy $typeOfPolicy
     * @param Car $car
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newAction(Request $request, TypeOfPolicy $typeOfPolicy, Car $car)
    {
        $refUrl = $request->query->get('ref', $request->getRequestUri());
        if (null === $car->getOwner()) {
            $this->addFlash('warning', 'Моля, изберете или въведете собственик на МПС.');
            return $this->redirectToRoute('car_new_owner', ['car' => $car->getId(), 'type' => 'owner', 'ref' => $refUrl]);
        }

        $policy = new Policy();
        $policy->setPolicyType($typeOfPolicy);
        $policy->setCar($car);
        $policy->setOwner($car->getOwner());
        $policy->setRepresentative($car->getRepresentative());

        $policy->addPayment(new Payment());
        // add 3 more payments
        for ($i = 3; $i <= 9; $i += 3) {
            $dueAt = (new \DateTime())->add(new \DateInterval('P' . $i . 'M'));
            $payment = new Payment();
            $payment->setDueAt($dueAt);
            $policy->addPayment($payment);
        }

        $form = $this->createForm(PolicyType::class, $policy);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        $data = [
            'policy' => $policy,
            'form' => $form->createView(),
            'policyType' => $typeOfPolicy,
            'car' => $car,
            'isNew' => true,
            'refUrl' => $refUrl
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->policyService->newPolicy($request, $policy);
                $this->addFlash('success', 'Полицата бе успешно създадена.');

                return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);

            } catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());

                return $this->render('policy/new.html.twig', $data);
            }
        }

        return $this->render('policy/new.html.twig', $data);
    }

    /**
     * Displays a form to edit an existing policy entity.
     *
     * @Route("/{id}/edit", name="policy_edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Policy $policy
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function editAction(Request $request, Policy $policy)
    {
        $refUrl = $request->getRequestUri();
        if (null === $policy->getCar()) {
            $this->addFlash('warning', 'Моля, изберете или въведете МПС.');
            return $this->redirectToRoute('policy_new_car', ['typeOfPolicy' => $policy->getPolicyType()->getId(), 'ref' => $refUrl]);
        }

        $form = $this->createForm(PolicyType::class, $policy);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        $data = [
            'policy' => $policy,
            'form' => $form->createView(),
            'car' => $policy->getCar(),
            'isNew' => false,
            'refUrl' => $refUrl,
            'canDelete' => $this->policyService->canDelete($policy)
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->policyService->editPolicy($request, $policy);
                $this->addFlash('success', 'Данните бяха успешно записани.');

                return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);

            } catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
                return $this->render('policy/edit.html.twig', $data);
            }
        }

        return $this->render('policy/edit.html.twig', $data);
    }

    /**
     * Deletes a policy entity.
     *
     * @Route("/{policy}", name="policy_delete", methods={"DELETE"}, requirements={"policy", "\d+"})
     * @param Policy $policy
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Policy $policy)
    {
        if (!$this->policyService->canDelete($policy)) {
            $this->addFlash('danger', 'Нямате права за тази операция.');
            return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);
        }

        $policyTypeId = $policy->getPolicyType()->getId();

        try {
            $this->policyService->deletePolicy($policy);
            $this->addFlash('success', 'Полицата бе успешно изтрита.');

            return $this->redirectToRoute('policy_list', ['typeOfPolicy' => $policyTypeId]);

        } catch (Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
            return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);
        }
    }

    /**
     * @Route("/{policy}/document/{document}/delete", name="document_delete", methods={"DELETE"}, requirements={"policy": "\d+", "document": "\d+"})
     * @param Policy $policy
     * @param Document $document
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteDocument(Policy $policy, Document $document)
    {
        try {
            $this->documentService->deleteDocument($document);
            $this->addFlash('success', 'Документът бе успешно изтрит.');

        } catch (Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }

        return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);
    }

    /**
     * @Route("/car/search", name="policy_search_car", methods={"GET"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function searchOwner(Request $request)
    {
        $q = $request->query->get('term');
        /** @var Car[]|null $cars */
        $cars = $this->carService->findByKeyword((string)$q);

        $data = [];
        if ($cars) {
            foreach ($cars as $car) {
                $data[] = [
                    'value' => $car->getId(),
                    'label' => $car->getIdNumber() . ': ' . $car->getCarMake() . ' ' . $car->getCarModel() . ', собств. ' . $car->getOwner()->getFullName()
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
            ->add('car', AutocompleteType::class, [
                'class' => Car::class,
                'label' => 'Изберете съществуващо МПС',
                'label_attr' => [
                    'class' => 'sr-only'
                ],
                'attr' => [
                    'placeholder' => 'Въведи рег. No, модел или собственик'
                ]
            ])
            ->getForm();
    }
}
