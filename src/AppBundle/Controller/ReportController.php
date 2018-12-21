<?php
declare(strict_types=1);

namespace AppBundle\Controller;

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
     * @Route("/payment")
     */
    public function paymentAction()
    {
        return $this->render('report/payment.html.twig', [

        ]);
    }

}
