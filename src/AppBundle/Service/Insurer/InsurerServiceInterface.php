<?php
declare(strict_types=1);

namespace AppBundle\Service\Insurer;

use AppBundle\Entity\Insurer;

/**
 * Interface InsurerServiceInterface
 * @package AppBundle\Service\Insurer
 */
interface InsurerServiceInterface
{
    /**
     * @param Insurer $insurer
     * @return Insurer
     * @throws \Exception
     */
    public function newInsurer(Insurer $insurer);

    /**
     * @param Insurer $insurer
     * @return Insurer
     */
    public function editInsurer(Insurer $insurer);

    /**
     * @param Insurer $insurer
     */
    public function deleteInsurer(Insurer $insurer);

    /**
     * @param $id
     * @return Insurer|object|null
     */
    public function find($id);
}
