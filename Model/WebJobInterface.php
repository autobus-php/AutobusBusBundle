<?php

namespace Autobus\Bundle\BusBundle\Model;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Execution;

/**
 * WebJobInterface
 */
interface WebJobInterface
{
    public function populateExecution(Execution $execution, Context $context);

    /**
     * @param array $methods
     *
     * @return Job
     */
    public function setMethods($methods);

    /**
     * @return array
     */
    public function getMethods();

    /**
     * @param boolean $secure
     *
     * @return Job
     */
    public function setSecure($secure);

    /**
     * @return boolean
     */
    public function isSecure();

    /**
     * @param string $path
     *
     * @return Job
     */
    public function setPath($path);

    /**
     * @return string
     */
    public function getPath();
}
