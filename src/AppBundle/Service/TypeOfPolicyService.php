<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\TypeOfPolicy;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class TypeOfPolicyService
 * @package AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class TypeOfPolicyService
{
    private $em;

    /**
     * TypeOfPolicyService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return TypeOfPolicy[]
     */
    public function getAll()
    {
        return $this->em->getRepository(TypeOfPolicy::class)
            ->findBy(['isDeleted' => 0], ['position' => 'ASC']);
    }
}