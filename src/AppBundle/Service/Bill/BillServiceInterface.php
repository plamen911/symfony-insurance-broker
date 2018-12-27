<?php
declare(strict_types=1);

namespace AppBundle\Service\Bill;

use AppBundle\Entity\Bill;
use AppBundle\Entity\Insurer;
use AppBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Interface BillServiceInterface
 * @package AppBundle\Service\Bill
 */
interface BillServiceInterface
{
    /**
     * @param Bill $bill
     * @return Bill
     * @throws \Exception
     */
    public function newBill(Bill $bill);

    /**
     * @param Bill $bill
     * @return Bill
     */
    public function editBill(Bill $bill);

    /**
     * @param Bill $bill
     */
    public function deleteBill(Bill $bill);

    /**
     * @param Insurer $insurer
     * @param array $range
     * @return Bill[]|ArrayCollection
     */
    public function getExistingByInsurerAndByRange(Insurer $insurer, array $range);

    /**
     * @param Bill $bill
     * @return Bill
     */
    public function save(Bill $bill);

    /**
     * @param Insurer $insurer
     * @param User|null $agent
     * @param \DateTime $givenAt
     * @param array $range
     * @return int
     * @throws \Exception
     */
    public function saveSuggested(Insurer $insurer, ?User $agent, \DateTime $givenAt, array $range);
}
