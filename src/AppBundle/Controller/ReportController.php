<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Payment;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class ReportController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("/report")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class ReportController extends Controller
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * ReportController constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/car/payment", name="report_car_payment", methods={"GET"})
     * @throws \Exception
     */
    public function paymentAction()
    {
        return $this->render('report/payment.html.twig', [
            'overdue_payments' => $this->getOverduePayments(),
            'payments_after_one_week' => $this->getPaymentsAfterOneWeek(),
            'payments_after_two_weeks' => $this->getPaymentsAfterTwoWeeks(),
            'payments_after_three_weeks' => $this->getPaymentsAfterThreeWeeks(),
        ]);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getOverduePayments()
    {
        $startDate = new \DateTime('-1 year');
        $endDate = new \DateTime();
        return $this->em->getRepository(Payment::class)->findAllByDateRange($startDate, $endDate);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getPaymentsAfterOneWeek()
    {
        $startDate = new \DateTime();
        $endDate = new \DateTime('+1 week');
        return $this->em->getRepository(Payment::class)->findAllByDateRange($startDate, $endDate);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getPaymentsAfterTwoWeeks()
    {
        $startDate = new \DateTime('+1 week');
        $endDate = new \DateTime('+2 week');
        return $this->em->getRepository(Payment::class)->findAllByDateRange($startDate, $endDate);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    private function getPaymentsAfterThreeWeeks()
    {
        $startDate = new \DateTime('+2 week');
        $endDate = new \DateTime('+3 week');
        return $this->em->getRepository(Payment::class)->findAllByDateRange($startDate, $endDate);
    }
}
