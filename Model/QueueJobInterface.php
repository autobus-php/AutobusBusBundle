<?php

namespace Autobus\Bundle\BusBundle\Model;

/**
 * QueueJobInterface
 */
interface QueueJobInterface
{
    /**
     * @param string $queue
     * @return Job
     */
    public function setQueue($queue);

    /**
     * @return string
     */
    public function getQueue();
}
