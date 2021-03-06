<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Car;
use AppBundle\Service\Cyr2Lat\Cyr2Lat;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * CarRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CarRepository extends \Doctrine\ORM\EntityRepository
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
     * @param Car $car
     * @return Car
     */
    public function save(Car $car)
    {
        if (null === $car->getId()) {
            $this->em->persist($car);
        }
        $this->em->flush();

        return $car;
    }

    /**
     * @param Car $car
     */
    public function delete(Car $car)
    {
        $this->em->remove($car);
        $this->em->flush();
    }

    /**
     * @param string $keyword
     * @return Car[]|null
     */
    public function findByKeyword(string $keyword)
    {
        $cyr2Lat = new Cyr2Lat();

        return $this->createQueryBuilder('c')
            ->leftJoin('c.owner', 'o')
            ->where('c.idNumber LIKE :idNumber')
            ->orWhere('c.carMake LIKE :keyword')
            ->orWhere('c.carModel LIKE :keyword')
            ->orWhere('o.firstName LIKE :keyword')
            ->orWhere('o.middleName LIKE :keyword')
            ->orWhere('o.lastName LIKE :keyword')
            ->setParameters([
                'idNumber' => $cyr2Lat->transliterate($keyword) . '%',
                'keyword' => $keyword . '%'
            ])
            ->addOrderBy('c.idNumber', 'ASC')
            ->addOrderBy('c.carMake', 'ASC')
            ->addOrderBy('c.carModel', 'ASC')
            ->getQuery()
            ->setFirstResult(0)
            ->setMaxResults(50)
            ->getResult();
    }
}
