<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Payment;
use AppBundle\Entity\User;
use AppBundle\Repository\PaymentRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ReportService
 * @package AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class ReportService implements ReportServiceInterface
{
    /** @var User $user */
    private $user;

    /** @var PaymentRepository $paymentRepo */
    private $paymentRepo;

    /**
     * ReportService constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param PaymentRepository $paymentRepo
     */
    public function __construct(TokenStorageInterface $tokenStorage, PaymentRepository $paymentRepo)
    {
        $this->user = $tokenStorage->getToken()->getUser();
        $this->paymentRepo = $paymentRepo;
    }

    /**
     * @param Payment $payment
     * @param bool $isReminded
     * @return Payment
     * @throws \Exception
     */
    public function reminder(Payment $payment, bool $isReminded)
    {
        $payment->setIsReminded($isReminded);
        if ($isReminded) {
            $payment->setRemindedAt(new \DateTime());
            $payment->setReminder($this->user);
        } else {
            $payment->setRemindedAt(null);
            $payment->setReminder(null);
        }
        $this->paymentRepo->save($payment);

        return $payment;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getOverduePayments()
    {
        $startDate = new \DateTime('-1 year');
        $endDate = new \DateTime('-1 day');
        return $this->paymentRepo->findAllByDateRange($startDate, $endDate);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentsAfterOneWeek()
    {
        $startDate = new \DateTime('-1 day');
        $endDate = new \DateTime('+1 week');
        return $this->paymentRepo->findAllByDateRange($startDate, $endDate);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentsAfterTwoWeeks()
    {
        $startDate = new \DateTime('+1 week');
        $endDate = new \DateTime('+2 week');
        return $this->paymentRepo->findAllByDateRange($startDate, $endDate);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentsAfterThreeWeeks()
    {
        $startDate = new \DateTime('+2 week');
        $endDate = new \DateTime('+3 week');
        return $this->paymentRepo->findAllByDateRange($startDate, $endDate);
    }
}
