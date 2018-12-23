<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Security $security
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Security $security)
    {
        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('policy_index');
        }

        return $this->redirectToRoute('security_login');
    }
}
