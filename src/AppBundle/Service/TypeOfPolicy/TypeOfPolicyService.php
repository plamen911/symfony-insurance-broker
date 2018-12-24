<?php
declare(strict_types=1);

namespace AppBundle\Service\TypeOfPolicy;

use AppBundle\Entity\TypeOfPolicy;
use AppBundle\Repository\TypeOfPolicyRepository;

/**
 * Class TypeOfPolicyService
 * @package AppBundle\Service\TypeOfPolicy
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class TypeOfPolicyService implements TypeOfPolicyServiceInterface
{
    private $typeOfPolicyRepo;

    /**
     * TypeOfPolicyService constructor.
     * @param TypeOfPolicyRepository $typeOfPolicyRepo
     */
    public function __construct(TypeOfPolicyRepository $typeOfPolicyRepo)
    {
        $this->typeOfPolicyRepo = $typeOfPolicyRepo;
    }

    /**
     * @return TypeOfPolicy[]|null
     */
    public function getAll()
    {
        return $this->typeOfPolicyRepo->findBy(['isDeleted' => 0], ['position' => 'ASC']);
    }
}