<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Autobus\Bundle\BusBundle\Model\Execution as BaseExecution;
use Doctrine\ORM\Mapping as ORM;

/**
 * Service call
 *
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\ExecutionRepository")
 */
class Execution extends BaseExecution
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Job
     *
     * @ORM\ManyToOne(targetEntity="Autobus\Bundle\BusBundle\Entity\Job", inversedBy="executions")
     */
    protected $job;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $caller;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $request;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $response;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $logs;

    /**
     * Duration in milliseconds
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $duration;

    /**
     * Call state
     *
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $state;

    /**
     * Started at microtime
     *
     * @var int
     */
    protected $startedAt;
}
