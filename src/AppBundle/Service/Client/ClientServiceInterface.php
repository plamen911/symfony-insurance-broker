<?php
declare(strict_types=1);

namespace AppBundle\Service\Client;

use AppBundle\Entity\Client;

/**
 * Interface ClientServiceInterface
 * @package AppBundle\Service\Client
 */
interface ClientServiceInterface
{
    /**
     * @return array
     */
    public function findAll();

    /**
     * @param string $keyword
     * @return Client[]|null
     */
    public function findByKeyword(string $keyword);

    /**
     * @param Client $client
     * @return Client
     */
    public function newClient(Client $client);

    /**
     * @param Client $client
     * @return Client
     */
    public function editClient(Client $client);

    /**
     * @param Client $client
     */
    public function deleteClient(Client $client);
}
