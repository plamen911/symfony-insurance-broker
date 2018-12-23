<?php
declare(strict_types=1);

namespace AppBundle\Repository;

use AppBundle\Entity\Role;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;

/**
 * RoleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RoleRepository extends \Doctrine\ORM\EntityRepository
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
     * @param Role $role
     * @return Role
     */
    public function save(Role $role)
    {
        if (null === $role->getId()) {
            $this->em->persist($role);
        }
        $this->em->flush();

        return $role;
    }

    /**
     * @param Role $role
     */
    public function delete(Role $role)
    {
        $this->em->remove($role);
        $this->em->flush();
    }
}
