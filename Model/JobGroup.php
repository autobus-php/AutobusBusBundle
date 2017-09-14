<?php

namespace Autobus\Bundle\BusBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;

/**
 * JobGroup
 */
abstract class JobGroup implements JobGroupInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var ArrayCollection
     */
    protected $jobs;

    public function __construct()
    {
        $this->jobs = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
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
     * @param string $name
     * @return JobGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
