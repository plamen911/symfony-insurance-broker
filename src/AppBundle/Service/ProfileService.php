<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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
    /** @var EntityManagerInterface $em */
    private $em;

    /** @var UserPasswordEncoder $encoder */
    private $encoder;

    /** @var User $user */
    private $user;

    /**
     * ReportService constructor.
     * @param EntityManagerInterface $em
     * @param UserPasswordEncoderInterface $encoder
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, TokenStorageInterface $tokenStorage)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->user = $tokenStorage->getToken()->getUser();
    }

    /**
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function editProfile(User $user)
    {
        $user->setUpdatedAt(new \DateTime());
        $this->em->flush();

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
