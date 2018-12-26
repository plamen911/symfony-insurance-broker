<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Bill;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * BillRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BillRepository extends \Doctrine\ORM\EntityRepository
{
    /** @var EntityManagerInterface $em */
    private $em;

    /**
     * CarRepository constructor.
     * @param EntityManagerInterface $em
     * @param ClassMetadata $class
     */
    public function __construct(EntityManagerInterface $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->em = $em;
    }

    /**
     * @param Bill $bill
     * @return Bill
     */
    public function save(Bill $bill)
    {
        if (null === $bill->getId()) {
            $this->em->persist($bill);
        }
        $this->em->flush();

        return $bill;
    }

    /**
     * @param Bill $bill
     */
    public function delete(Bill $bill)
    {
        $this->em->remove($bill);
        $this->em->flush();
    }

    /**
     * @param Bill $bill
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isValidAndVacant(Bill $bill)
    {
        return 0 < (int)$this->createQueryBuilder('b')
                ->select('count(b.id)')
                ->where('b.idNumber = :idNumber')
                ->andWhere('b.policyId IS NULL OR b.policyId = 0')
                ->setParameter('idNumber', $bill->getIdNumber())
                ->getQuery()
                ->getSingleScalarResult();
    }
}
