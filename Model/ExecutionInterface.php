<?php
namespace Autobus\Bundle\BusBundle\Model;

/**
 * Execution
 */
interface ExecutionInterface
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * @param Job $job
     *
     * @return Execution
     */
    public function setJob(Job $job);

    /**
     * @return Job
     */
    public function getJob();

    /**
     * @param \DateTime $date
     *
     * @return Execution
     */
    public function setDate($date);

    /**
     * @return \DateTime
     */
    public function getDate();

    /**
     * @param string $caller
     *
     * @return Execution
     */
    public function setCaller($caller);

    /**
     * @return string
     */
    public function getCaller();

    /**
     * @param string $request
     *
     * @return Execution
     */
    public function setRequest($request);

    /**
     * @return string
     */
    public function getRequest();

    /**
     * @param string $response
     *
     * @return Execution
     */
    public function setResponse($response);

    /**
     * @return string
     */
    public function getResponse();

    /**
     * @param int $duration
     *
     * @return Execution
     */
    public function setDuration($duration);

    /**
     * @return int
     */
    public function getDuration();

    /**
     * Start counter
     */
    public function start();

    /**
     * Stop counter
     */
    public function finish();

    /**
     * @param string $state
     *
     * @return Execution
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $logs
     *
     * @return Execution
     */
    public function setLogs($logs);

    /**
     * @return string
     */
    public function getLogs();
}
