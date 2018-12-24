<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\ProfileType;
use AppBundle\Form\UserType;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use AppBundle\Service\Profile\ProfileServiceInterface;
use AppBundle\Service\Role\RoleServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
//
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Omines\DataTablesBundle\Column\BoolColumn;
use Omines\DataTablesBundle\Controller\DataTablesTrait;

/**
 * Class UserController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * Notice - This way the user registration is disabled
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class UserController extends Controller
{
    use DataTablesTrait;

    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var ProfileServiceInterface $profileService */
    private $profileService;

    /** @var RoleServiceInterface $roleService */
    private $roleService;

    /**
     * UserController constructor.
     * @param FormErrorServiceInterface $formErrorService
     * @param ProfileServiceInterface $profileService
     * @param RoleServiceInterface $roleService
     */
    public function __construct(FormErrorServiceInterface $formErrorService, ProfileServiceInterface $profileService, RoleServiceInterface $roleService)
    {
        $this->formErrorService = $formErrorService;
        $this->profileService = $profileService;
        $this->roleService = $roleService;
    }

    /**
     * Lists all car users.
     *
     * @Route("/user/", name="user_index", methods={"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // https://omines.github.io/datatables-bundle/
        $table = $this->createDataTable([
            'stateSave' => true,
            'pageLength' => 25,
            'autoWidth' => true,
            'searching' => true,
        ])
            ->add('fullName', TextColumn::class, ['label' => 'Име'])
            ->add('email', TextColumn::class, ['label' => 'И-мейл'])
            ->add('roles', TextColumn::class, [
                'searchable' => false,
                'label' => 'Роли',
                'render' => function ($value, $user) {
                    $output = '<ul class="list-unstyled">';
                    /** @var User $user */
                    foreach ($user->getProfileRoles() as $role) {
                        $output .= '<li>' . $role->getTitle() . '</li>';
                    }
                    $output .= '</ul>';
                    return $output;
                }
            ])
            ->add('createdAt', DateTimeColumn::class, [
                'searchable' => false,
                'format' => 'd.m.Y H:i:s',
                'label' => 'Добавен на',
            ])
            ->add('enabled', BoolColumn::class, [
                'label' => 'Активен?',
                'className' => 'text-center',
                'render' => function ($value, $user) {
                    $output = '';
                    /** @var User $user */
                    if ($user->isEnabled()) {
                        $output .= '<span class="badge badge-pill badge-success">Да</span>';
                    } else {
                        $output .= '<span class="badge badge-pill badge-danger">Не</span>';
                    }
                    return $output;
                }
            ])
            ->add('buttons', TextColumn::class, [
                'label' => '',
                'searchable' => false,
                'className' => 'text-center',
                'render' => function ($value, $user) {
                    /** @var User $user */
                    return '<a href="' . $this->generateUrl('user_edit', ['user' => $user->getId()]) . '" class="btn btn-sm btn-secondary" title="Редактирай"><i class="fas fa-edit"></i></a>';
                }
            ])
            ->createAdapter(ORMAdapter::class, [
                'entity' => User::class
            ])
            ->handleRequest($request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }

        return $this->render('user/index.html.twig', [
            'datatable' => $table
        ]);
    }

    /**
     * @Route("/user/new", name="user_new", methods={"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function newAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(ProfileType::class, $user, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->profileService->newProfile($user);
                $this->addFlash('success', 'Профилът бе успешно създаден.');

                return $this->redirectToRoute('user_edit', ['user' => $user->getId()]);

            } catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());

                return $this->render('user/new.html.twig', [
                    'form' => $form->createView()
                ]);
            }
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{user}/edit", name="user_edit", methods={"GET", "POST"}, requirements={"user": "\d+"})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function editAction(Request $request, User $user)
    {
        if ($user->getId() === $this->getUser()->getId()) {
            return $this->redirectToRoute('profile_edit');
        }

        $form = $this->createForm(ProfileType::class, $user, ['user' => $this->getUser()]);
        $form->handleRequest($request);

        $this->formErrorService->checkErrors($form);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (true === $this->profileService->changePassword($form, $user)) {
                    $this->addFlash('success', 'Паролата бе успешно променена.');
                }
            } catch (\Exception $ex) {
                $this->addFlash('danger', $ex->getMessage());

                return $this->render('user/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }

            $this->profileService->editProfile($user);
            $this->addFlash('success', 'Профилът бе успешно редактиран.');

            return $this->redirectToRoute('user_edit', ['user' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
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
            $userRole = $this->roleService->findOneBy(['name' => 'ROLE_ADMIN']);
            $user->addRole($userRole);
            $this->profileService->newProfile($user);

            $token = new UsernamePasswordToken(
                $user,
                $user->getPassword(),
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
