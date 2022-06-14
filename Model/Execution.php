<?php

namespace Autobus\Bundle\BusBundle\Model;

use Autobus\Bundle\BusBundle\Entity\TopicJob;

/**
 * Execution
 */
abstract class Execution implements ExecutionInterface
{
    const STATE_SUCCESS = 'success';
    const STATE_ERROR = 'error';
    const STATE_UNKNOWN = 'unknown';
    const LOG_TYPE_INFO = 'info';
    const LOG_TYPE_WARNING = 'warning';
    const LOG_TYPE_ERROR = 'error';

    /**
     * @var int
     */
    protected $id;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $caller;

    /**
     * @var string
     */
    protected $request;

    /**
     * @var string
     */
    protected $response;

    /**
     * @var string
     */
    protected $logs;

    /**
     * Duration in milliseconds
     *
     * @var int
     */
    protected $duration;

    /**
     * Call state
     *
     * @var string
     */
    protected $state;

    /**
     * Started at microtime
     *
     * @var int
     */
    protected $startedAt;

    /**
     * Must be saved at end?
     *
     * @var bool
     */
    protected $mustBeSaved;

    public function __construct()
    {
        $this->state       = self::STATE_UNKNOWN;
        $this->mustBeSaved = true;
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
     * @param string $type
     * @param string $message
     *
     * @return Execution
     */
    public function addLog($type, $message)
    {
        // Generate log
        $logAsString = sprintf(
            '[%s] %s',
            $type,
            $message
        );

        // Write log
        if (!empty($this->getLogs())) {
            $logs = sprintf(
                "%s\n%s",
                $this->getLogs(),
                $logAsString
            );
        } else {
            $logs = $logAsString;
        }
        $this->setLogs($logs);


        return $this;
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

    /**
     * @return bool
     */
    public function mustBeSaved(): bool
    {
        return $this->mustBeSaved;
    }

    /**
     * @param bool $mustBeSaved
     *
     * @return Execution
     */
    public function setMustBeSaved(bool $mustBeSaved): Execution
    {
        $this->mustBeSaved = $mustBeSaved;

        return $this;
    }

    /**
     * Only executions from topic job can be rerun
     *
     * @return bool
     */
    public function canBeReRun(): bool
    {
        return $this->job instanceof TopicJob && $this->state === self::STATE_ERROR;
    }
}
