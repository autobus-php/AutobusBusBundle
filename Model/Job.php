<?php

namespace Autobus\Bundle\BusBundle\Model;

use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Entity\JobGroup;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Job
 */
abstract class Job implements JobInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    protected $runner;

    /**
     * @var string
     */
    protected $config;

    /**
     * @var array
     */
    protected $configArray;

    /**
     * @var bool
     */
    protected $trace;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     */
    protected $executions;

    /**
     * @var Execution
     */
    protected $lastExecution;

    /**
     * @var JobGroup
     */
    protected $group;
}
