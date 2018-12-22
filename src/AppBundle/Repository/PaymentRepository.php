<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Policy;

/**
 * PaymentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaymentRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param int $limit
     * @return mixed
     */
    public function findAllByDateRange(\DateTime $startDate, \DateTime $endDate, int $limit = 200)
    {
        return $this->createQueryBuilder('payment')
            ->select('payment')
            ->addSelect('policy')
            ->addSelect('policyType')
            ->addSelect('agent')
            ->addSelect('insurer')
            ->addSelect('owner')
            ->addSelect('representative')
            ->addSelect('car')
            ->leftJoin('payment.policy', 'policy')
            ->leftJoin('policy.policyType', 'policyType')
            ->leftJoin('policy.agent', 'agent')
            ->leftJoin('policy.insurer', 'insurer')
            ->leftJoin('policy.owner', 'owner')
            ->leftJoin('policy.representative', 'representative')
            ->leftJoin('policy.car', 'car')
            ->where('payment.dueAt >= :startDate')
            ->andWhere('payment.dueAt < :endDate')
            ->andWhere('payment.amountDue > payment.amountPaid')
            ->setParameter('startDate', $startDate, \Doctrine\DBAL\Types\Type::DATETIME)
            ->setParameter('endDate', $endDate, \Doctrine\DBAL\Types\Type::DATETIME)
            ->orderBy('payment.dueAt', 'ASC')
            ->orderBy('payment.paymentOrder', 'ASC')
            ->getQuery()
            ->setFirstResult(0)
            ->setMaxResults($limit)
            ->getResult();
    }
}
