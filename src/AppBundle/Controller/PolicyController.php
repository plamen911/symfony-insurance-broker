<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Car;
use AppBundle\Entity\Document;
use AppBundle\Entity\Payment;
use AppBundle\Entity\Policy;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Form\PolicyType;
use AppBundle\Service\Aws\UploadInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

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
    /** @var UploadInterface $uploadService */
    private $uploadService;

    /**
     * PolicyController constructor.
     * @param UploadInterface $uploadService
     */
    public function __construct(UploadInterface $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * @Route("/", name="policy_index", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function defaultPolicyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $typeOfPolicy = $em->getRepository(TypeOfPolicy::class)
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
        $em = $this->getDoctrine()->getManager();
        $policies = $em->getRepository(Policy::class)->findBy(['policyType' => $typeOfPolicy->getId()]);

        return $this->render('policy/index.html.twig', [
            'policies' => $policies,
            'typeOfPolicy' => $typeOfPolicy
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

        $tplData = [
            'policy' => $policy,
            'form' => $form->createView(),
            'policyType' => $typeOfPolicy,
            'car' => $car,
            'isNew' => true
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->validateForm($policy);
            } catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
                return $this->render('policy/new.html.twig', $tplData);
            }

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

            $em = $this->getDoctrine()->getManager();
            $em->persist($policy);
            $em->flush();

            $this->addFlash('success', 'Полицата бе успешно създадена.');

            return $this->redirectToRoute('policy_show', ['id' => $policy->getId()]);
        }

        return $this->render('policy/new.html.twig', $tplData);
    }

    /**
     * Finds and displays a policy entity.
     *
     * @Route("/{id}", name="policy_show")
     * @Method("GET")
     * @param Policy $policy
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Policy $policy)
    {
        $deleteForm = $this->createDeleteForm($policy);

        return $this->render('policy/show.html.twig', array(
            'policy' => $policy,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing policy entity.
     *
     * @Route("/{id}/edit", name="policy_edit", methods={"GET", "POST"}, requirements={"id": "\d+"})
     * @param Request $request
     * @param Policy $policy
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Policy $policy)
    {
        $deleteForm = $this->createDeleteForm($policy);
        $form = $this->createForm(PolicyType::class, $policy);
        $form->handleRequest($request);

        $tplData = [
            'policy' => $policy,
            'form' => $form->createView(),
            'car' => $policy->getCar(),
            'delete_form' => $deleteForm->createView(),
            'isNew' => false
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->validateForm($policy);
            } catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());
                return $this->render('policy/edit.html.twig', $tplData);
            }

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

            $policy->setUpdatedAt(new \DateTime());
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Данните бяха успешно записани.');

            return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);
        }

        return $this->render('policy/edit.html.twig', $tplData);
    }

    /**
     * Deletes a policy entity.
     *
     * @Route("/{id}", name="policy_delete", methods={"DELETE"}, requirements={"id", "\d+"})
     * @param Request $request
     * @param Policy $policy
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Policy $policy)
    {
        $form = $this->createDeleteForm($policy);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($policy);
            $em->flush();
        }

        return $this->redirectToRoute('policy_index');
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
            $this->uploadService->delete(basename($document->getFileUrl()));
            $em = $this->getDoctrine()->getManager();
            $em->remove($document);
            $em->flush();
            $this->addFlash('success', 'Документът бе успешно изтрит.');

        } catch (Exception $ex) {
            $this->addFlash('danger', $ex->getMessage());
        }

        return $this->redirectToRoute('policy_edit', ['id' => $policy->getId()]);
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
            ->setAction($this->generateUrl('policy_delete', array('id' => $policy->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Policy $policy
     * @throws Exception
     */
    private function validateForm(Policy $policy)
    {
        $totalDue = 0;
        foreach ($policy->getPayments() as $payment) {
            $totalDue += (float)$payment->getAmountDue();
        }
        if ($policy->getTotal() !== $totalDue) {
            throw new Exception('Общо дължима премия (' . $policy->getTotal() . ') е различна от сумата на вноските (' . $totalDue . ').');
        }
    }
}
