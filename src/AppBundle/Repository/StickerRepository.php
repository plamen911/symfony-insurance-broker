<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Insurer;
use AppBundle\Entity\Sticker;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * StickerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StickerRepository extends \Doctrine\ORM\EntityRepository
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
     * @param Sticker $sticker
     * @return Sticker
     */
    public function save(Sticker $sticker)
    {
        if (null === $sticker->getId()) {
            $this->em->persist($sticker);
        }
        $this->em->flush();

        return $sticker;
    }

    /**
     * @param Sticker $sticker
     */
    public function delete(Sticker $sticker)
    {
        $this->em->remove($sticker);
        $this->em->flush();
    }

    /**
     * @param Sticker $sticker
     * @return bool
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function isValidAndVacant(Sticker $sticker)
    {
        return 0 < (int)$this->createQueryBuilder('s')
                ->select('count(s.id)')
                ->where('s.idNumber = :idNumber')
                ->andWhere('s.policyId IS NULL OR s.policyId = 0')
                ->setParameter('idNumber', $sticker->getIdNumber())
                ->getQuery()
                ->getSingleScalarResult();
    }

    /**
     * @param Insurer $insurer
     * @param array $range
     * @return Sticker[]|ArrayCollection
     */
    public function getExistingByInsurerAndByRange(Insurer $insurer, array $range)
    {
        return $this->createQueryBuilder('s')
            ->leftJoin('s.insurer', 'i')
            ->where('s.idNumber IN (:range)')
            // ->where('i.id != :insurerId AND s.idNumber IN (:range)')
            // ->setParameter('insurerId', $insurer->getId())
            ->setParameter('range', $range, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();
    }
}
