<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\TypeOfPolicy;

/**
 * Interface TypeOfPolicyServiceInterface
 * @package AppBundle\Service
 */
interface TypeOfPolicyServiceInterface
{
    /**
     * @return TypeOfPolicy[]
     */
    public function getAll();
}
