<?php
declare(strict_types=1);

namespace AppBundle\Service\Role;

use AppBundle\Entity\Role;
use AppBundle\Repository\RoleRepository;

/**
 * Class RoleService
 * @package AppBundle\Service\Role
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class RoleService implements RoleServiceInterface
{
    /** @var RoleRepository $roleRepo */
    private $roleRepo;

    /**
     * RoleService constructor.
     * @param RoleRepository $roleRepo
     */
    public function __construct(RoleRepository $roleRepo)
    {
        $this->roleRepo = $roleRepo;
    }

    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return Role|object|null
     */
    public function findOneBy(array $criteria, array $orderBy = null)
    {
        return $this->roleRepo->findOneBy($criteria, $orderBy);
    }
}