<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * QueueJob
 *
 * @ORM\Table(name="job_queue")
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\QueueJobRepository")
 */
class QueueJob extends Job
{
    /**
     * @var string
     *
     * @ORM\Column(name="queue", type="string")
     */
    private $queue;

    /**
     * @param string $queue
     * @return QueueJob
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return string
     */
    public function getQueue()
    {
        return $this->queue;
    }
}
