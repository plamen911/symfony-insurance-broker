<?php
declare(strict_types=1);

namespace AppBundle\Service\TypeOfPolicy;

use AppBundle\Entity\TypeOfPolicy;

/**
 * Interface TypeOfPolicyServiceInterface
 * @package AppBundle\Service\TypeOfPolicy
 */
interface TypeOfPolicyServiceInterface
{
    /**
     * @return TypeOfPolicy[]|null
     */
    public function getAll();
}
