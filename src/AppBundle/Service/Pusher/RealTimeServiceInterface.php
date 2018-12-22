<?php
declare(strict_types=1);

namespace AppBundle\Service\Pusher;

use Exception;

/**
 * Interface RealTimeServiceInterface
 * @package AppBundle\Service\Pusher
 */
interface RealTimeServiceInterface
{
    /**
     * @param array $data
     * @return $this
     * @throws Exception
     */
    public function dispatch(array $data);

    /**
     * @param string $event
     * @return $this
     */
    public function setEvent(string $event);
}
