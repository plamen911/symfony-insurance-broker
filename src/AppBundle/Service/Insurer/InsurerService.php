<?php
declare(strict_types=1);

namespace AppBundle\Service\Insurer;

use AppBundle\Entity\Insurer;
use AppBundle\Entity\User;
use AppBundle\Repository\InsurerRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class InsurerService
 * @package AppBundle\Service\Insurer
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class InsurerService implements InsurerServiceInterface
{
    /** @var User $currentUser */
    private $currentUser;

    /** @var InsurerRepository $insurerRepo */
    private $insurerRepo;

    /**
     * InsurerService constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param InsurerRepository $insurerRepo
     */
    public function __construct(TokenStorageInterface $tokenStorage, InsurerRepository $insurerRepo)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->insurerRepo = $insurerRepo;
    }

    /**
     * @param Insurer $insurer
     * @return Insurer
     * @throws \Exception
     */
    public function newInsurer(Insurer $insurer)
    {
        $this->insurerRepo->save($insurer);

        return $insurer;
    }

    /**
     * @param Insurer $insurer
     * @return Insurer
     */
    public function editInsurer(Insurer $insurer)
    {
        $this->insurerRepo->save($insurer);

        return $insurer;
    }

    /**
     * @param Insurer $insurer
     */
    public function deleteInsurer(Insurer $insurer)
    {
        $this->insurerRepo->delete($insurer);
    }

    /**
     * @param $id
     * @return Insurer|object|null
     */
    public function find($id)
    {
        return $this->insurerRepo->find($id);
    }
}
