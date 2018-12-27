<?php
declare(strict_types=1);

namespace AppBundle\Service\Sticker;

use AppBundle\Entity\Insurer;
use AppBundle\Entity\Sticker;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;

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
     * @param Request $request
     * @param Sticker $sticker
     * @return Sticker
     */
    public function editSticker(Request $request, Sticker $sticker);

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
}
