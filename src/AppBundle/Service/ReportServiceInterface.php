<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Payment;

/**
 * Interface ReportServiceInterface
 * @package AppBundle\Service
 */
interface ReportServiceInterface
{
    /**
     * @param Payment $payment
     * @param bool $isReminded
     * @return Payment
     * @throws \Exception
     */
    public function reminder(Payment $payment, bool $isReminded);

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getOverduePayments();

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentsAfterOneWeek();

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentsAfterTwoWeeks();

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getPaymentsAfterThreeWeeks();
}
