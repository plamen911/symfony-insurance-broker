<?php
declare(strict_types=1);

namespace AppBundle\Service\Policy;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Document;
use AppBundle\Entity\GreenCard;
use AppBundle\Entity\Policy;
use AppBundle\Entity\Sticker;
use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Entity\User;
use AppBundle\Repository\BillRepository;
use AppBundle\Repository\GreenCardRepository;
use AppBundle\Repository\PolicyRepository;
use AppBundle\Repository\StickerRepository;
use AppBundle\Repository\TypeOfPolicyRepository;
use AppBundle\Service\Aws\UploadInterface;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class PolicyService
 * @package AppBundle\Service\Policy
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class PolicyService implements PolicyServiceInterface
{
    /** @var User $currentUser */
    private $currentUser;

    /** @var PolicyRepository */
    private $policyRepo;

    /** @var TypeOfPolicyRepository $typeOfPolicyRepo */
    private $typeOfPolicyRepo;

    /** @var GreenCardRepository $greenCardRepo */
    private $greenCardRepo;

    /** @var StickerRepository $stickerRepo */
    private $stickerRepo;

    /** @var BillRepository $billRepo */
    private $billRepo;

    /** @var UploadInterface $uploadService */
    private $uploadService;

    /**
     * ReportService constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param PolicyRepository $policyRepo
     * @param TypeOfPolicyRepository $typeOfPolicyRepo
     * @param GreenCardRepository $greenCardRepo
     * @param StickerRepository $stickerRepo
     * @param BillRepository $billRepo
     * @param UploadInterface $uploadService
     */
    public function __construct(TokenStorageInterface $tokenStorage, PolicyRepository $policyRepo, TypeOfPolicyRepository $typeOfPolicyRepo, GreenCardRepository $greenCardRepo, StickerRepository $stickerRepo, BillRepository $billRepo, UploadInterface $uploadService)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->policyRepo = $policyRepo;
        $this->typeOfPolicyRepo = $typeOfPolicyRepo;
        $this->greenCardRepo = $greenCardRepo;
        $this->stickerRepo = $stickerRepo;
        $this->billRepo = $billRepo;
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
        $this->processUpload($request, $policy);

        $policy->getCar()->setUpdatedAt(new \DateTime());
        $policy->getCar()->setUpdater($this->currentUser);

        $policy->setPaid($policy->getPaidTotal());
        $policy->setBalance($policy->getBalanceTotal());
        $policy->setAuthor($this->currentUser);
        $policy->setUpdater($this->currentUser);
        $this->policyRepo->save($policy);

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
        $this
            ->validatePayments($policy)
            ->validateGreenCards($policy)
            ->validateStickers($policy)
            ->validateBills($policy)
            ->processUpload($request, $policy);

        $policy
            ->getCar()
            ->setUpdatedAt(new \DateTime())
            ->setUpdater($this->currentUser);

        $policy
            ->setPaid($policy->getPaidTotal())
            ->setBalance($policy->getBalanceTotal())
            ->setUpdatedAt(new \DateTime())
            ->setUpdater($this->currentUser);

        $this->policyRepo->save($policy);

        return $policy;
    }

    /**
     * @param Policy $policy
     */
    public function deletePolicy(Policy $policy)
    {
        $this->policyRepo->delete($policy);
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
     * @return PolicyService
     * @throws Exception
     */
    private function validatePayments(Policy $policy)
    {
        $totalDue = 0;
        foreach ($policy->getPayments() as $i => $payment) {
            if (null === $payment->getDueAt()) {
                throw new Exception('Липсва дата за падеж No ' . ($i + 1) . '.');
            }
            $amountDue = (float)$payment->getAmountDue();
            if (0 >= $amountDue) {
                throw new Exception('Липсва дължима сума за падеж No ' . ($i + 1) . '.');
            }
            $amountPaid = $payment->getAmountPaid();
            if ($amountPaid > 0) {
                if ($amountPaid !== $amountDue) {
                    throw new Exception(sprintf('Платената сума (%.2f) за падеж No %d не може да е различна от дължимата сума (%.2f).', $amountPaid, ($i + 1), $amountDue));
                }
                if (null === $payment->getPaidAt()) {
                    throw new Exception('Липсва дата на плащане за падеж No ' . ($i + 1) . '.');
                }
            }

            $totalDue += $amountDue;
            $payment->setPaymentOrder($i + 1);
            $policy->getPayments()->set($i, $payment);
        }

        if (round($policy->getTotal(), 2) !== round($totalDue, 2)) {
            throw new Exception('Общо дължима премия (' . $policy->getTotal() . ') е различна от сумата на вноските (' . $totalDue . ').');
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return PolicyService
     * @throws Exception
     */
    private function validateGreenCards(Policy $policy)
    {
        $seenInNumbers = [];
        foreach ($policy->getGreenCards() as $i => $greenCard) {
            if (empty($greenCard->getIdNumber())) {
                throw new Exception('Липсва номер на зелена карта ' . ($i + 1) . '.');
            }

            $existingGreenCard = $this->greenCardRepo->findOneBy(['idNumber' => $greenCard->getIdNumber()]);
            if (null === $existingGreenCard) {
                throw new Exception($greenCard->getIdNumber() . ' е невалиден номер на зелена карта.');
            }

            if (in_array($greenCard->getIdNumber(), $seenInNumbers)) {
                throw new Exception('Зелена карта ' . $greenCard->getIdNumber() . ' вече е добавена.');
            }

            $seenInNumbers[] = $greenCard->getIdNumber();

            if (null === $greenCard->getId()) {
                $policy->removeGreenCard($greenCard);
                /** @var GreenCard $existingGreenCard */
                $existingGreenCard->setPolicy($greenCard->getPolicy());
                $existingGreenCard->setPrice($greenCard->getPrice());
                $existingGreenCard->setTax($greenCard->getTax());
                $existingGreenCard->setAmountDue($greenCard->getAmountDue());
                $policy->addGreenCard($existingGreenCard);
            }
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return $this
     * @throws Exception
     */
    private function validateStickers(Policy $policy)
    {
        $seenInNumbers = [];
        foreach ($policy->getStickers() as $i => $sticker) {
            if (empty($sticker->getIdNumber())) {
                throw new Exception('Липсва номер на стикер ' . ($i + 1) . '.');
            }

            $existingSticker = $this->stickerRepo->findOneBy(['idNumber' => $sticker->getIdNumber()]);
            if (null === $existingSticker) {
                throw new Exception($sticker->getIdNumber() . ' е невалиден номер на стикер.');
            }

            if (in_array($sticker->getIdNumber(), $seenInNumbers)) {
                throw new Exception('Стикер ' . $sticker->getIdNumber() . ' вече е добавен.');
            }

            $seenInNumbers[] = $sticker->getIdNumber();

            if (null === $sticker->getId()) {
                $policy->removeSticker($sticker);
                /** @var Sticker $existingSticker */
                $existingSticker->setPolicy($sticker->getPolicy());
                $existingSticker->setIsCancelled($sticker->getIsCancelled());
                $policy->addSticker($existingSticker);
            }
        }

        return $this;
    }

    /**
     * @param Policy $policy
     * @return $this
     * @throws Exception
     */
    private function validateBills(Policy $policy)
    {
        $seenInNumbers = [];
        foreach ($policy->getBills() as $i => $bill) {
            if (empty($bill->getIdNumber())) {
                throw new Exception('Липсва номер на сметка ' . ($i + 1) . '.');
            }

            $existingBill = $this->billRepo->findOneBy(['idNumber' => $bill->getIdNumber()]);
            if (null === $existingBill) {
                throw new Exception($bill->getIdNumber() . ' е невалиден номер на сметка.');
            }

            if (in_array($bill->getIdNumber(), $seenInNumbers)) {
                throw new Exception('Сметка ' . $bill->getIdNumber() . ' вече е добавена.');
            }

            $seenInNumbers[] = $bill->getIdNumber();

            if (null === $bill->getId()) {
                $policy->removeBill($bill);
                /** @var Bill $existingBill */
                $existingBill->setPolicy($bill->getPolicy());
                $existingBill->setPrice($bill->getPrice());
                $policy->addBill($existingBill);
            }
        }

        return $this;
    }

    /**
     * Upload car documents
     *
     * @param Request $request
     * @param Policy $policy
     * @return PolicyService
     */
    private function processUpload(Request $request, Policy $policy): PolicyService
    {
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

        return $this;
    }
}
