<?php
declare(strict_types=1);

namespace AppBundle\Service\Sticker;

use AppBundle\Entity\User;
use AppBundle\Repository\StickerRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class StickerService
 * @package AppBundle\Service\Sticker
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class StickerService implements StickerServiceInterface
{
    /** @var User $currentUser */
    private $currentUser;

    /** @var StickerRepository $stickerRepo */
    private $stickerRepo;

    /**
     * StickerService constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param StickerRepository $stickerRepo
     */
    public function __construct(TokenStorageInterface $tokenStorage, StickerRepository $stickerRepo)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->stickerRepo = $stickerRepo;
    }

}
