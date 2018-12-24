<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ProfileService
 * @package AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class ProfileService implements ProfileServiceInterface
{
    /** @var UserPasswordEncoder $encoder */
    private $encoder;

    /** @var User $user */
    private $user;

    /** @var UserRepository $userRepo */
    private $userRepo;

    /**
     * ReportService constructor.
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenStorageInterface $tokenStorage
     * @param UserRepository $userRepo
     */
    public function __construct(UserPasswordEncoderInterface $encoder, TokenStorageInterface $tokenStorage, UserRepository $userRepo)
    {
        $this->encoder = $encoder;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->userRepo = $userRepo;
    }

    /**
     * @param User $user
     * @return User
     * @throws \Exception
     */
    public function newProfile(User $user)
    {
        if (0 === count($user->getRoles())) {
            throw new \Exception('Профилът трябва да има поне една роля.');
        }

        $password = $this->encoder->encodePassword($user, $user->getPassword());
        $user->setPassword($password);
        $this->userRepo->save($user);

        return $user;
    }

    /**
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function editProfile(User $user)
    {
        if (0 === count($user->getRoles())) {
            throw new \Exception('Профилът трябва да има поне една роля.');
        }

        $user->setUpdatedAt(new \DateTime());
        $this->userRepo->save($user);

        return $user;
    }

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function changePassword(FormInterface $form, User $user)
    {
        $oldPassword = $form->get('old_password')->getData();
        $newPassword = $form->get('new_password')->getData();
        // Change user password
        if (!empty($oldPassword) && !empty($newPassword)) {
            if (!$this->encoder->isPasswordValid($user, $oldPassword)) {
                throw new Exception('Грешна стара парола!');
            }
            $user->setPassword($this->encoder->encodePassword($user, $newPassword));

            return true;
        }

        return false;
    }
}
