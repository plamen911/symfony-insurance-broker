<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Exception;
use Symfony\Component\Form\FormInterface;

/**
 * Interface ProfileServiceInterface
 * @package AppBundle\Service
 */
interface ProfileServiceInterface
{
    /**
     * @param User $user
     * @return User
     * @throws Exception
     */
    public function editProfile(User $user);

    /**
     * @param FormInterface $form
     * @param User $user
     * @return bool
     * @throws Exception
     */
    public function changePassword(FormInterface $form, User $user);
}
