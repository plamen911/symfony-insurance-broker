<?php
declare(strict_types=1);

namespace AppBundle\Service\Pusher;

use Pusher\Pusher;

/**
 * Class PusherService
 * @package AppBundle\Service\Pusher
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class PusherService implements RealTimeServiceInterface
{
    /** @var Pusher $pusher */
    private $pusher;

    /** @var string $channel */
    private $channel;

    /** @var string|null $event */
    private $event;

    /**
     * PusherService constructor.
     * @param Pusher $pusher
     * @param string $channel
     */
    public function __construct(Pusher $pusher, string $channel)
    {
        $this->pusher = $pusher;
        $this->channel = $channel;
        $this->event = null;
    }

    /**
     * @param array $data
     * @return $this
     * @throws \Exception
     */
    public function dispatch(array $data)
    {
        if (null === $this->event) {
            throw new \Exception('Event name is missing.');
        }

        $this->pusher->trigger($this->channel, $this->event, $data);

        return $this;
    }

    /**
     * @param string $event
     * @return $this
     */
    public function setEvent(string $event)
    {
        $this->event = $event;

        return $this;
    }
}
