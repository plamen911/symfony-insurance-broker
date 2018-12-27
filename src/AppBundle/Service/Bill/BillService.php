<?php
declare(strict_types=1);

namespace AppBundle\Service\Bill;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Insurer;
use AppBundle\Entity\User;
use AppBundle\Repository\BillRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class BillService
 * @package AppBundle\Service\Bill
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class BillService implements BillServiceInterface
{
    /** @var User $currentUser */
    private $currentUser;

    /** @var BillRepository $billRepo */
    private $billRepo;

    /**
     * GreenCardService constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param BillRepository $billRepo
     */
    public function __construct(TokenStorageInterface $tokenStorage, BillRepository $billRepo)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->billRepo = $billRepo;
    }

    /**
     * @param Bill $bill
     * @return Bill
     * @throws \Exception
     */
    public function newBill(Bill $bill)
    {
        $bill->setCreatedAt(new \DateTime());
        $bill->setAuthor($this->currentUser);
        $this->billRepo->save($bill);

        return $bill;
    }

    /**
     * @param Bill $bill
     * @return Bill
     */
    public function editBill(Bill $bill)
    {
        $this->billRepo->save($bill);

        return $bill;
    }

    /**
     * @param Bill $bill
     */
    public function deleteBill(Bill $bill)
    {
        $this->billRepo->delete($bill);
    }

    /**
     * @param Insurer $insurer
     * @param array $range
     * @return Bill[]|ArrayCollection
     */
    public function getExistingByInsurerAndByRange(Insurer $insurer, array $range)
    {
        return $this->billRepo->getExistingByInsurerAndByRange($insurer, $range);
    }

    /**
     * @param Bill $bill
     * @return Bill
     */
    public function save(Bill $bill)
    {
        return $this->billRepo->save($bill);
    }

    /**
     * @param Insurer $insurer
     * @param User|null $agent
     * @param \DateTime $givenAt
     * @param array $range
     * @return int
     * @throws \Exception
     */
    public function saveSuggested(Insurer $insurer, ?User $agent, \DateTime $givenAt, array $range)
    {
        $existing = array_map(function ($bill) {
            /** @var Bill $bill */
            return $bill->getIdNumber();
        }, $this->getExistingByInsurerAndByRange($insurer, $range));

        $count = 0;
        foreach ($range as $idNumber) {
            if (empty($idNumber) || in_array($idNumber, $existing)) continue;

            $bill = new Bill();
            $bill->setIdNumber($idNumber);
            $bill->setInsurer($insurer);
            $bill->setAgent($agent);
            $bill->setGivenAt((null === $agent) ? null : $givenAt);
            $bill->setReceivedAt(new \DateTime());
            $bill->setAuthor($this->currentUser);
            $this->save($bill);
            $count++;
        }

        return $count;
    }
}
