<?php
declare(strict_types=1);

namespace AppBundle\Service\GreenCard;

use AppBundle\Entity\GreenCard;
use AppBundle\Entity\Insurer;
use AppBundle\Entity\User;
use AppBundle\Repository\GreenCardRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class GreenCardService
 * @package AppBundle\Service\GreenCard
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class GreenCardService implements GreenCardServiceInterface
{
    /** @var User $currentUser */
    private $currentUser;

    /** @var GreenCardRepository $greenCardRepo */
    private $greenCardRepo;

    /**
     * GreenCardService constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param GreenCardRepository $greenCardRepo
     */
    public function __construct(TokenStorageInterface $tokenStorage, GreenCardRepository $greenCardRepo)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->greenCardRepo = $greenCardRepo;
    }

    /**
     * @param GreenCard $greenCard
     * @return GreenCard
     * @throws \Exception
     */
    public function newGreenCard(GreenCard $greenCard)
    {
        $greenCard->setCreatedAt(new \DateTime());
        $greenCard->setAuthor($this->currentUser);
        $this->greenCardRepo->save($greenCard);

        return $greenCard;
    }

    /**
     * @param GreenCard $greenCard
     * @return GreenCard
     */
    public function editGreenCard(GreenCard $greenCard)
    {
        $this->greenCardRepo->save($greenCard);

        return $greenCard;
    }

    /**
     * @param GreenCard $greenCard
     */
    public function deleteGreenCard(GreenCard $greenCard)
    {
        $this->greenCardRepo->delete($greenCard);
    }

    /**
     * @param Insurer $insurer
     * @param array $range
     * @return GreenCard[]|ArrayCollection
     */
    public function getExistingByInsurerAndByRange(Insurer $insurer, array $range)
    {
        return $this->greenCardRepo->getExistingByInsurerAndByRange($insurer, $range);
    }

    /**
     * @param GreenCard $greenCard
     * @return GreenCard
     */
    public function save(GreenCard $greenCard)
    {
        return $this->greenCardRepo->save($greenCard);
    }

    /**
     * @param Insurer $insurer
     * @param User $agent
     * @param \DateTime $givenAt
     * @param array $range
     * @return int
     * @throws \Exception
     */
    public function saveSuggested(Insurer $insurer, User $agent, \DateTime $givenAt, array $range)
    {
        $existing = array_map(function ($greenCard) {
            /** @var GreenCard $greenCard */
            return $greenCard->getIdNumber();
        }, $this->getExistingByInsurerAndByRange($insurer, $range));

        $count = 0;
        foreach ($range as $idNumber) {
            if (empty($idNumber) || in_array($idNumber, $existing)) continue;

            $greenCard = new GreenCard();
            $greenCard->setIdNumber($idNumber);
            $greenCard->setInsurer($insurer);
            $greenCard->setAgent($agent);
            $greenCard->setGivenAt((null === $agent) ? null : $givenAt);
            $greenCard->setReceivedAt(new \DateTime());
            $greenCard->setAuthor($this->currentUser);
            $this->save($greenCard);
            $count++;
        }

        return $count;
    }
}
