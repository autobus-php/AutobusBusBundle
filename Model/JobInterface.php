<?php

namespace Autobus\Bundle\BusBundle\Model;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Execution;

/**
 */
interface JobInterface
{
    public function updatedTimestamps();

    public function populateExecution(Execution $execution, Context $context);

    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Job
     */
    public function setName($name);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set service
     *
     * @param string $runner
     *
     * @return Job
     */
    public function setRunner($runner);

    /**
     * Get service
     *
     * @return string
     */
    public function getRunner();

    /**
     * @param string $config JSON config
     *
     * @return Job
     */
    public function setConfig($config);


    /**
     * @return string
     */
    public function getConfig();


    /**
     * * @return array
     */
    public function getConfigArray();

    /**
     * @param array $config
     *
     * @return Job
     */
    public function setConfigArray($config);

    /**
     * @param boolean $trace
     *
     * @return Job
     */
    public function setTrace($trace);

    /**
     * @return boolean
     */
    public function getTrace();

    /**
     * @param mixed $executions
     *
     * @return Job
     */
    public function setExecutions($executions);

    /**
     * @return mixed
     */
    public function getExecutions();

    /**
     * @param mixed $createdAt
     *
     * @return Job
     */
    public function setCreatedAt($createdAt);

    /**
     * @return mixed
     */
    public function getCreatedAt();

    /**
     * @param \DateTime $updatedAt
     *
     * @return Job
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @return \DateTime
     */
    public function getUpdatedAt();

    /**
     * @param mixed $group
     * @return Job
     */
    public function setGroup($group);

    /**
     * @return mixed
     */
    public function getGroup();

    /**
     * Get job type
     *
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getState();

    /**
     * Get last execution
     *
     * @return Execution
     */
    public function getLastExecution();
}
