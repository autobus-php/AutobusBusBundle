<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Service call
 *
 * @ORM\Table(name="job_execution")
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\ExecutionRepository")
 */
class Execution
{
    const STATE_SUCCESS = 'success';
    const STATE_ERROR = 'error';
    const STATE_UNKNOWN = 'unknown';

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Autobus\Bundle\BusBundle\Entity\Job", inversedBy="executions")
     * @ORM\JoinColumn(name="service", referencedColumnName="id")
     */
    private $job;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="caller", type="string")
     */
    private $caller;

    /**
     * @var string
     *
     * @ORM\Column(name="request", type="text", nullable=true)
     */
    private $request;

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="text", nullable=true)
     */
    private $response;

    /**
     * @var string
     *
     * @ORM\Column(name="logs", type="text", nullable=true)
     */
    private $logs;

    /**
     * Duration in milliseconds
     *
     * @var int
     *
     * @ORM\Column(name="duration", type="integer")
     */
    private $duration;

    /**
     * Call state
     *
     * @var string
     *
     * @ORM\Column(name="state", type="string")
     */
    private $state;

    /**
     * Started at microtime
     *
     * @var int
     */
    protected $startedAt;

    public function __construct()
    {
        $this->state = self::STATE_UNKNOWN;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Job $job
     *
     * @return Execution
     */
    public function setJob(Job $job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param \DateTime $date
     *
     * @return Execution
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param string $caller
     *
     * @return Execution
     */
    public function setCaller($caller)
    {
        $this->caller = $caller;

        return $this;
    }

    /**
     * @return string
     */
    public function getCaller()
    {
        return $this->caller;
    }

    /**
     * @param string $request
     *
     * @return Execution
     */
    public function setRequest($request)
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param string $response
     *
     * @return Execution
     */
    public function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param int $duration
     *
     * @return Execution
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Start counter
     */
    public function start()
    {
        $this->startedAt = microtime(true);
    }

    /**
     * Stop counter
     */
    public function finish()
    {
        $this->duration = round((microtime(true) - $this->startedAt) * 1000);
    }

    /**
     * @param string $state
     *
     * @return Execution
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param string $logs
     *
     * @return Execution
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogs()
    {
        return $this->logs;
    }
}
