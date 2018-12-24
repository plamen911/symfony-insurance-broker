<?php
declare(strict_types=1);

namespace AppBundle\Service\Role;

use AppBundle\Entity\Role;

/**
 * Class RoleService
 * @package AppBundle\Service\Role
 * @author Plamen Markov <plamen@lynxlake.org>
 */
interface RoleServiceInterface
{
    /**
     * @param array $criteria
     * @param array|null $orderBy
     * @return Role|object|null
     */
    public function findOneBy(array $criteria, array $orderBy = null);
}
