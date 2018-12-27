<?php
declare(strict_types=1);

namespace AppBundle\Service\GreenCard;

use AppBundle\Entity\GreenCard;
use AppBundle\Entity\Insurer;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface GreenCardServiceInterface
 * @package AppBundle\Service\GreenCard
 */
interface GreenCardServiceInterface
{
    /**
     * @param GreenCard $greenCard
     * @return GreenCard
     * @throws \Exception
     */
    public function newGreenCard(GreenCard $greenCard);

    /**
     * @param GreenCard $greenCard
     * @return GreenCard
     */
    public function editGreenCard(GreenCard $greenCard);

    /**
     * @param GreenCard $greenCard
     */
    public function deleteGreenCard(GreenCard $greenCard);

    /**
     * @param Insurer $insurer
     * @param array $range
     * @return GreenCard[]|ArrayCollection
     */
    public function getExistingByInsurerAndByRange(Insurer $insurer, array $range);

    /**
     * @param GreenCard $greenCard
     * @return GreenCard
     */
    public function save(GreenCard $greenCard);

    /**
     * @param Insurer $insurer
     * @param User $agent
     * @param \DateTime $givenAt
     * @param array $range
     * @throws \Exception
     */
    public function saveSuggested(Insurer $insurer, User $agent, \DateTime $givenAt, array $range);
}
