<?php
declare(strict_types=1);

namespace AppBundle\Service\Client;

use AppBundle\Entity\Client;
use AppBundle\Entity\User;
use AppBundle\Repository\ClientRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ClientService
 * @package AppBundle\Service\Client
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class ClientService implements ClientServiceInterface
{
    /** @var User $currentUser */
    private $currentUser;

    /** @var ClientRepository $clientRepo */
    private $clientRepo;

    /**
     * ReportService constructor.
     * @param TokenStorageInterface $tokenStorage
     * @param ClientRepository $clientRepo
     */
    public function __construct(TokenStorageInterface $tokenStorage, ClientRepository $clientRepo)
    {
        $this->currentUser = $tokenStorage->getToken()->getUser();
        $this->clientRepo = $clientRepo;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->clientRepo->findAll();
    }

    /**
     * @param string $keyword
     * @return Client[]|null
     */
    public function findByKeyword(string $keyword)
    {
        return $this->clientRepo->findByKeyword($keyword);
    }

    /**
     * @param Client $client
     * @return Client
     */
    public function newClient(Client $client)
    {
        $this->clientRepo->save($client);

        return $client;
    }

    /**
     * @param Client $client
     * @return Client
     * @throws \Exception
     */
    public function editClient(Client $client)
    {
        $client->setUpdatedAt(new \DateTime());
        $this->clientRepo->save($client);

        return $client;
    }

    /**
     * @param Client $client
     */
    public function deleteClient(Client $client)
    {
        $this->clientRepo->delete($client);
    }
}