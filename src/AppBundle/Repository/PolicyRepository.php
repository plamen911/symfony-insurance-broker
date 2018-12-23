<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Policy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * PolicyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PolicyRepository extends \Doctrine\ORM\EntityRepository
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
     * @param Policy $policy
     * @return Policy
     */
    public function save(Policy $policy)
    {
        if (null === $policy->getId()) {
            $this->em->persist($policy);
        }
        $this->em->flush();

        return $policy;
    }

    /**
     * @param Policy $policy
     */
    public function delete(Policy $policy)
    {
        $this->em->remove($policy);
        $this->em->flush();
    }
}
