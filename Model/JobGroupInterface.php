<?php
namespace Autobus\Bundle\BusBundle\Model;

/**
 * JobGroup
 */
interface JobGroupInterface
{
    /**
     * Get id
     *
     * @return int
     */
    public function getId();

    /**
     * @param string $name
     * @return JobGroup
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();
}
