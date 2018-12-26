<?php
declare(strict_types=1);

namespace AppBundle\Service\Sticker;

use AppBundle\Entity\Sticker;
use AppBundle\Entity\User;
use AppBundle\Repository\StickerRepository;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @param Sticker $sticker
     * @return Sticker
     * @throws \Exception
     */
    public function newSticker(Sticker $sticker)
    {
        $sticker->setCreatedAt(new \DateTime());
        $sticker->setAuthor($this->currentUser);
        $this->stickerRepo->save($sticker);

        return $sticker;
    }

    /**
     * @param Request $request
     * @param Sticker $sticker
     * @return Sticker
     */
    public function editSticker(Request $request, Sticker $sticker)
    {
        $this->stickerRepo->save($sticker);

        return $sticker;
    }

    /**
     * @param Sticker $sticker
     */
    public function deleteSticker(Sticker $sticker)
    {
        $this->stickerRepo->delete($sticker);
    }
}
