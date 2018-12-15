<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Car;
use AppBundle\Entity\Document;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Policy;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Entity\User;
use AppBundle\Form\CarType;
use AppBundle\Form\PolicyType;
use AppBundle\Service\Aws\UploadInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use PUGX\AutocompleterBundle\Form\Type\AutocompleteType;

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
     * @Route("/", name="policy_index", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function redirectToListAction()
    {
        $typeOfPolicy = $this->em->getRepository(TypeOfPolicy::class)
            ->findOneBy(['isDeleted' => 0], ['position' => 'ASC']);

        return $this->redirectToRoute("policy_list", ['typeOfPolicy' => $typeOfPolicy->getId()]);
    }

    /**
     * Lists all policy entities.
     *
     * @Route("/{typeOfPolicy}", name="policy_list", methods={"GET"}, requirements={"typeOfPolicy": "\d+"})
     * @param TypeOfPolicy $typeOfPolicy
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(TypeOfPolicy $typeOfPolicy)
    {
        $policies = $this->em->getRepository(Policy::class)->findBy(['policyType' => $typeOfPolicy->getId()]);

        return $this->render('policy/index.html.twig', [
            'policies' => $policies,
            'typeOfPolicy' => $typeOfPolicy
        ]);
    }

    /**
     * @Route("/new/type/{typeOfPolicy}", name="policy_new_car", methods={"GET", "POST"}, requirements={"typeOfPolicy": "\d+"})
     * @param Request $request
     * @param TypeOfPolicy $typeOfPolicy
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newCarAction(Request $request, TypeOfPolicy $typeOfPolicy)
    {
        $refUrl = $request->query->get('ref');

        $autoCompleteForm = $this->createAutoCompleteForm();
        $autoCompleteForm->handleRequest($request);

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
        $refUrl = $request->query->get('ref');

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
                $this->validatePayments($policy);
            } catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
                return $this->render('policy/new.html.twig', $data);
            }

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
                    $policy->getCar()->addDocument($document);
                }
            }

            $policy->setPaid($policy->getPaidTotal());
            $policy->setBalance($policy->getBalanceTotal());
            $policy->setAuthor($this->getUser());
            $policy->setUpdater($this->getUser());
            $this->em->persist($policy);
            $this->em->flush();

            $this->addFlash('success', 'Полицата бе успешно създадена.');

            return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);
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

        $canDelete = $this->getUser()->isAdmin() || $this->getUser()->isPolicyAuthor($policy);

        $deleteForm = $this->createDeleteForm($policy);
        $form = $this->createForm(PolicyType::class, $policy);
        $form->handleRequest($request);

        $data = [
            'policy' => $policy,
            'form' => $form->createView(),
            'car' => $policy->getCar(),
            'delete_form' => $deleteForm->createView(),
            'isNew' => false,
            'refUrl' => $refUrl,
            'canDelete' => $canDelete
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->validatePayments($policy);
            } catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
                return $this->render('policy/edit.html.twig', $data);
            }

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
                    $policy->getCar()->addDocument($document);
                }
            }

            $policy->setPaid($policy->getPaidTotal());
            $policy->setBalance($policy->getBalanceTotal());
            $policy->setUpdatedAt(new \DateTime());
            $policy->setUpdater($this->getUser());
            $this->em->flush();

            $this->addFlash('success', 'Данните бяха успешно записани.');

            return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);
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
        try {
            $this->em->remove($policy);
            $this->em->flush();

            $this->addFlash('success', 'Полицата бе успешно изтрита.');

            return $this->redirectToRoute('policy_index');

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
        $canDelete = $this->getUser()->isAdmin() || $this->getUser()->isPolicyAuthor($policy);
        if (!$canDelete) {
            $this->addFlash('danger', 'Нямате права за тази операция.');
            return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);
        }

        try {
            $this->uploadService->delete(basename($document->getFileUrl()));
            $this->em->remove($document);
            $this->em->flush();
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
        $cars = $this->em->getRepository(Car::class)->findByKeyword((string)$q);

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
     * Creates a form to delete a policy entity.
     *
     * @param Policy $policy The policy entity
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteForm(Policy $policy)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('policy_delete', array('policy' => $policy->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Policy $policy
     * @throws Exception
     */
    private function validatePayments(Policy $policy)
    {
        $totalDue = 0;
        foreach ($policy->getPayments() as $payment) {
            $totalDue += (float)$payment->getAmountDue();
        }

        if (round($policy->getTotal(), 2) !== round($totalDue, 2)) {
            throw new Exception('Общо дължима премия (' . $policy->getTotal() . ') е различна от сумата на вноските (' . $totalDue . ').');
        }
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
