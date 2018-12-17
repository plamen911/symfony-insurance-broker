<?php
declare(strict_types=1);

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\ProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var UserPasswordEncoder $encoder */
    private $encoder;

    /**
     * ProfileController constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $this->em = $em;
        $this->encoder = $encoder;
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
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldPassword = $request->get('old_password');
            $newPassword = $request->get('new_password');
            // Change user password
            if (!empty($oldPassword) && !empty($newPassword)) {
                if (!$this->encoder->isPasswordValid($user, $oldPassword)) {
                    $this->addFlash('danger', 'Грешна стара парола!');

                    return $this->render('profile/edit.html.twig', [
                        'user' => $user,
                        'form' => $form->createView(),
                    ]);
                }
                $user->setPassword($this->encoder->encodePassword($user, $newPassword));
                $this->addFlash('success', 'Паролата бе успешно променена.');
            }

            $user->setUpdatedAt(new \DateTime());
            $this->em->flush();

            $this->addFlash('success', 'Профилът бе успешно редактиран.');

            return $this->redirectToRoute('profile_edit');
        }

        return $this->render('profile/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
