<?php
declare(strict_types=1);

namespace AppBundle\Service;

use AppBundle\Entity\Insurer;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class InsurerService
 * @package AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class InsurerService
{
    private $em;

    /**
     * InsurerService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return Insurer[]
     */
    public function getAll()
    {
        return $this->em->getRepository(Insurer::class)
            ->findBy(['isDeleted' => 0], ['position' => 'ASC']);
    }
}