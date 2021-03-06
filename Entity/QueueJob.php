<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Autobus\Bundle\BusBundle\Model\QueueJobInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * QueueJob
 *
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\QueueJobRepository")
 */
class QueueJob extends Job implements QueueJobInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     *
     * @ORM\Column(type="string")
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
