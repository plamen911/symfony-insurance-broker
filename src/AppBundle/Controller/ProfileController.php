<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\ProfileType;
use AppBundle\Service\FormError\FormErrorServiceInterface;
use AppBundle\Service\Profile\ProfileServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package AppBundle\Controller
 * @author Plamen Markov <plamen@lynxlake.org>
 *
 * @Route("profile")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
 */
class ProfileController extends Controller
{
    /** @var FormErrorServiceInterface $formErrorService */
    private $formErrorService;

    /** @var ProfileServiceInterface $profileService */
    private $profileService;

    /**
     * ProfileController constructor.
     * @param FormErrorServiceInterface $formErrorsService
     * @param ProfileServiceInterface $profileService
     */
    public function __construct(FormErrorServiceInterface $formErrorsService, ProfileServiceInterface $profileService)
    {
        $this->formErrorService = $formErrorsService;
        $this->profileService = $profileService;
    }

    /**
     * @Route("/", methods={"GET", "POST"}, name="profile_edit")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function editAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getUser();
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

                return $this->render('profile/edit.html.twig', [
                    'user' => $user,
                    'form' => $form->createView(),
                ]);
            }

            $this->profileService->editProfile($user);
            $this->addFlash('success', 'Профилът бе успешно редактиран.');

            return $this->redirectToRoute('profile_edit');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
