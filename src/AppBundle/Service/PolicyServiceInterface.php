<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Policy;
use AppBundle\Entity\TypeOfPolicy;
use Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * Interface PolicyServiceInterface
 * @package AppBundle\Service
 */
interface PolicyServiceInterface
{
    /**
     * @return object|null|TypeOfPolicy
     */
    public function getDefaultTypeOfPolicy();

    /**
     * @param Request $request
     * @param Policy $policy
     * @return Policy
     * @throws Exception
     */
    public function newPolicy(Request $request, Policy $policy);

    /**
     * @param Request $request
     * @param Policy $policy
     * @return Policy
     * @throws Exception
     */
    public function editPolicy(Request $request, Policy $policy);

    /**
     * @param Policy $policy
     */
    public function deletePolicy(Policy $policy);

    /**
     * @param Policy $policy
     * @return bool
     */
    public function canDelete(Policy $policy);
}
