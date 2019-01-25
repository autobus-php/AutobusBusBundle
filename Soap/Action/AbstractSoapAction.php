<?php

namespace Autobus\Bundle\BusBundle\Soap\Action;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Entity\Job;
use Psr\Log\LoggerInterface;

/**
 * AbstractSoapAction
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Soap\Action
 */
abstract class AbstractSoapAction
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Job
     */
    protected $job;

    /**
     * @var Execution
     */
    protected $execution;

    /**
     * SoapAction constructor.
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
     * @return Context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @param Context $context
     *
     * @return AbstractSoapAction
     */
    public function setContext(Context $context)
    {
        $this->context = $context;

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
     * @param Job $job
     *
     * @return AbstractSoapAction
     */
    public function setJob(Job $job)
    {
        $this->job = $job;

        return $this;
    }

    /**
     * @return Execution
     */
    public function getExecution()
    {
        return $this->execution;
    }

    /**
     * @param Execution $execution
     *
     * @return AbstractSoapAction
     */
    public function setExecution(Execution $execution)
    {
        $this->execution = $execution;

        return $this;
    }
}