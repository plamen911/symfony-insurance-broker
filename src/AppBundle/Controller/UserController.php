<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Service\FormErrorServiceInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class UserController extends Controller
{
    /** @var EntityManagerInterface $em */
    private $em;
    /** @var UserPasswordEncoder $encoder */
    private $encoder;
    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /**
     * UserController constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param FormErrorServiceInterface $formErrorService
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, FormErrorServiceInterface $formErrorService)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->formErrorService = $formErrorService;
    }

    /**
     * @Route("/register", name="user_register")
     * @param Request $request
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface $session
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function registerAction(Request $request, TokenStorageInterface $tokenStorage, SessionInterface $session)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $userRole = $this->em->getRepository(Role::class)->findOneBy(['name' => 'ROLE_ADMIN']);
            $user->addRole($userRole);

            $this->em->persist($user);
            $this->em->flush();

            $token = new UsernamePasswordToken(
                $user,
                $password,
                'main',
                $user->getRoles()
            );

            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));

            $this->addFlash('success', 'Сега сте успешно регистриран.');

            return $this->redirectToRoute('policy_index');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
