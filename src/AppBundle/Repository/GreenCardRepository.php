<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\GreenCard;
use AppBundle\Entity\Insurer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * GreenCardRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GreenCardRepository extends \Doctrine\ORM\EntityRepository
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
     * @param GreenCard $greenCard
     * @return GreenCard
     */
    public function save(GreenCard $greenCard)
    {
        if (null === $greenCard->getId()) {
            $this->em->persist($greenCard);
        }
        $this->em->flush();

        return $greenCard;
    }

    /**
     * @param GreenCard $greenCard
     */
    public function delete(GreenCard $greenCard)
    {
        $this->em->remove($greenCard);
        $this->em->flush();
    }

    /**
     * @param GreenCard $greenCard
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isValidAndVacant(GreenCard $greenCard)
    {
        return 0 < (int)$this->createQueryBuilder('g')
                ->select('count(g.id)')
                ->where('g.idNumber = :idNumber')
                ->andWhere('g.policyId IS NULL OR g.policyId = 0')
                ->setParameter('idNumber', $greenCard->getIdNumber())
                ->getQuery()
                ->getSingleScalarResult();
    }

    /**
     * @param Insurer $insurer
     * @param array $range
     * @return GreenCard[]|ArrayCollection
     */
    public function getExistingByInsurerAndByRange(Insurer $insurer, array $range)
    {
        return $this->createQueryBuilder('g')
            ->leftJoin('g.insurer', 'i')
            ->where('g.idNumber IN (:range)')
            // ->where('i.id != :insurerId AND g.idNumber IN (:range)')
            // ->setParameter('insurerId', $insurer->getId())
            ->setParameter('range', $range, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();
    }
}
