<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Document;
use AppBundle\Entity\Policy;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Entity\User;
use AppBundle\Repository\PolicyRepository;
use AppBundle\Repository\TypeOfPolicyRepository;
use AppBundle\Service\Aws\UploadInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class PolicyService
 * @package AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class PolicyService implements PolicyServiceInterface
{
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var User $currentUser */
    private $currentUser;

    /** @var PolicyRepository */
    private $policyRepo;

    /** @var TypeOfPolicyRepository $typeOfPolicyRepo */
    private $typeOfPolicyRepo;

    /** @var UploadInterface $uploadService */
    private $uploadService;

    /**
     * ReportService constructor.
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface $tokenStorage
     * @param PolicyRepository $policyRepo
     * @param TypeOfPolicyRepository $typeOfPolicyRepo
     * @param UploadInterface $uploadService
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, PolicyRepository $policyRepo, TypeOfPolicyRepository $typeOfPolicyRepo, UploadInterface $uploadService)
    {
        $this->em = $em;
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->policyRepo = $policyRepo;
        $this->typeOfPolicyRepo = $typeOfPolicyRepo;
        $this->uploadService = $uploadService;
    }

    /**
     * @return object|null|TypeOfPolicy
     */
    public function getDefaultTypeOfPolicy()
    {
        return $this->typeOfPolicyRepo->findOneBy(['isDeleted' => 0], ['position' => 'ASC']);
    }

    /**
     * @param Request $request
     * @param Policy $policy
     * @return Policy
     * @throws Exception
     */
    public function newPolicy(Request $request, Policy $policy)
    {
        $this->validatePayments($policy);

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

        $policy->getCar()->setUpdatedAt(new \DateTime());
        $policy->getCar()->setUpdater($this->currentUser);

        $policy->setPaid($policy->getPaidTotal());
        $policy->setBalance($policy->getBalanceTotal());
        $policy->setAuthor($this->currentUser);
        $policy->setUpdater($this->currentUser);
        $this->em->persist($policy);
        $this->em->flush();

        return $policy;
    }

    /**
     * @param Request $request
     * @param Policy $policy
     * @return Policy
     * @throws Exception
     */
    public function editPolicy(Request $request, Policy $policy)
    {
        $this->validatePayments($policy);

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

        $policy->getCar()->setUpdatedAt(new \DateTime());
        $policy->getCar()->setUpdater($this->currentUser);

        $policy->setPaid($policy->getPaidTotal());
        $policy->setBalance($policy->getBalanceTotal());
        $policy->setUpdatedAt(new \DateTime());
        $policy->setUpdater($this->currentUser);
        $this->em->flush();

        return $policy;
    }

    /**
     * @param Policy $policy
     */
    public function deletePolicy(Policy $policy)
    {
        $this->em->remove($policy);
        $this->em->flush();
    }

    /**
     * @param Policy $policy
     * @return bool
     */
    public function canDelete(Policy $policy)
    {
        return $this->currentUser->isAdmin() || (null !== $policy->getAuthor() && $this->currentUser->getId() === $policy->getAuthor()->getId());
    }

    /**
     * @param Policy $policy
     * @throws Exception
     */
    private function validatePayments(Policy $policy)
    {
        $totalDue = 0;
        foreach ($policy->getPayments() as $i => $payment) {
            $totalDue += (float)$payment->getAmountDue();
            $payment->setPaymentOrder($i + 1);
            $policy->getPayments()->set($i, $payment);
        }

        if (round($policy->getTotal(), 2) !== round($totalDue, 2)) {
            throw new Exception('Общо дължима премия (' . $policy->getTotal() . ') е различна от сумата на вноските (' . $totalDue . ').');
        }
    }

}
