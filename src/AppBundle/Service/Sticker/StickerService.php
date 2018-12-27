<?php
declare(strict_types=1);

namespace AppBundle\Service\Sticker;

use AppBundle\Entity\Insurer;
use AppBundle\Entity\Sticker;
use AppBundle\Entity\User;
use AppBundle\Repository\StickerRepository;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @param Sticker $sticker
     * @return Sticker
     */
    public function editSticker(Sticker $sticker)
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

    /**
     * @param Insurer $insurer
     * @param array $range
     * @return Sticker[]|ArrayCollection
     */
    public function getExistingByInsurerAndByRange(Insurer $insurer, array $range)
    {
        return $this->stickerRepo->getExistingByInsurerAndByRange($insurer, $range);
    }

    /**
     * @param Sticker $sticker
     * @return Sticker
     */
    public function save(Sticker $sticker)
    {
        return $this->stickerRepo->save($sticker);
    }

    /**
     * @param Insurer $insurer
     * @param User $agent
     * @param \DateTime $givenAt
     * @param array $range
     * @throws \Exception
     */
    public function saveSuggested(Insurer $insurer, User $agent, \DateTime $givenAt, array $range)
    {
        $existing = array_map(function ($sticker) {
            /** @var Sticker $sticker */
            return $sticker->getIdNumber();
        }, $this->getExistingByInsurerAndByRange($insurer, $range));

        foreach ($range as $idNumber) {
            if (empty($idNumber) || in_array($idNumber, $existing)) continue;

            $sticker = new Sticker();
            $sticker->setIdNumber($idNumber);
            $sticker->setInsurer($insurer);
            $sticker->setAgent($agent);
            $sticker->setGivenAt((null === $agent) ? null : $givenAt);
            $sticker->setReceivedAt(new \DateTime());
            $sticker->setAuthor($this->currentUser);
            $this->save($sticker);
        }
    }
}
