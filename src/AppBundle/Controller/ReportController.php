<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Payment;
use AppBundle\Service\ReportServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    /** @var ReportServiceInterface $reportService */
    private $reportService;

    /**
     * ReportController constructor.
     * @param ReportServiceInterface $reportService
     */
    public function __construct(ReportServiceInterface $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * @Route("/car/payment", name="report_car_payment", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function paymentAction()
    {
        return $this->render('report/payment.html.twig', [
            'overdue_payments' => $this->reportService->getOverduePayments(),
            'payments_after_one_week' => $this->reportService->getPaymentsAfterOneWeek(),
            'payments_after_two_weeks' => $this->reportService->getPaymentsAfterTwoWeeks(),
            'payments_after_three_weeks' => $this->reportService->getPaymentsAfterThreeWeeks(),
        ]);
    }

    /**
     * @Route("/car/payment/{payment}/remind", name="report_car_payment_remind", methods={"POST"}, requirements={"payment": "\d+"})
     * @param Request $request
     * @param Payment $payment
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function reminderAction(Request $request, Payment $payment)
    {
        $isReminded = 1 === (int)$request->request->get('isReminded', 0);
        $payment->setIsReminded($isReminded);
        $this->reportService->reminder($payment, $isReminded);

        return $this->json([
            'isReminded' => $payment->getIsReminded() ? 1 : 0,
            'reminder' => null !== $payment->getReminder() ? $payment->getReminder()->getFullName() : '',
            'remindedAt' => null !== $payment->getRemindedAt() ? $payment->getRemindedAt()->format('d.m.Y H:i:s') : '',
            'carMake' => null !== $payment->getPolicy()->getCar() ?  $payment->getPolicy()->getCar()->getCarMake() : '',
            'carModel' => null !== $payment->getPolicy()->getCar() ?  $payment->getPolicy()->getCar()->getCarModel() : '',
            'carOwner' => null !== $payment->getPolicy()->getOwner() ?  $payment->getPolicy()->getOwner()->getFullName() : '',
            'paymentOrder' => $payment->getPaymentOrder(),
            'dueAt' => null !== $payment->getDueAt() ? $payment->getDueAt()->format('d.m.Y') : '',
            'policyType' => null !== $payment->getPolicy()->getPolicyType() ? $payment->getPolicy()->getPolicyType()->getName() : ''
        ], Response::HTTP_OK);
    }
}
