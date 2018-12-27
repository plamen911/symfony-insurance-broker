<?php
declare(strict_types=1);

namespace AppBundle\Service\Sticker;

use AppBundle\Entity\Insurer;
use AppBundle\Entity\Sticker;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface StickerServiceInterface
 * @package AppBundle\Service\Sticker
 */
interface StickerServiceInterface
{
    /**
     * @param Sticker $sticker
     * @return Sticker
     * @throws \Exception
     */
    public function newSticker(Sticker $sticker);

    /**
     * @param Sticker $sticker
     * @return Sticker
     */
    public function editSticker(Sticker $sticker);

    /**
     * @param Sticker $sticker
     */
    public function deleteSticker(Sticker $sticker);

    /**
     * @param Insurer $insurer
     * @param array $range
     * @return Sticker[]|ArrayCollection
     */
    public function getExistingByInsurerAndByRange(Insurer $insurer, array $range);

    /**
     * @param Sticker $sticker
     * @return Sticker
     */
    public function save(Sticker $sticker);

    /**
     * @param Insurer $insurer
     * @param User $agent
     * @param \DateTime $givenAt
     * @param array $range
     * @return int
     * @throws \Exception
     */
    public function saveSuggested(Insurer $insurer, User $agent, \DateTime $givenAt, array $range);
}
