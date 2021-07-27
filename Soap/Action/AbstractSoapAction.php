<?php

namespace Autobus\Bundle\BusBundle\Soap\Action;

use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Model\ExecutionInterface;
use Autobus\Bundle\BusBundle\Model\JobInterface;
use Psr\Log\LoggerInterface;

/**
 * AbstractSoapAction
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Soap\Action
 */
abstract class AbstractSoapAction implements SoapActionInterface
{
    /**
     * @var Job
     */
    protected $job;

    /**
     * @var ExecutionInterface
     */
    protected $execution;

    /**
     * Get stdClass object as array
     *
     * @param \stdClass $object
     *
     * @return array
     */
    public function getObjectAsArray(\stdClass $object)
    {
        return json_decode(json_encode($object), true);
    }

    /**
     * @return Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param JobInterface $job
     *
     * @return AbstractSoapAction
     */
    public function setJob(JobInterface $job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return ExecutionInterface
     */
    public function getExecution()
    {
        return $this->execution;
    }

    /**
     * @param ExecutionInterface $execution
     *
     * @return $this
     */
    public function setExecution(ExecutionInterface $execution)
    {
        $this->execution = $execution;

        return $this;
    }
}