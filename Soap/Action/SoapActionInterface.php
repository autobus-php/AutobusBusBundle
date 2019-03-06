<?php

namespace Autobus\Bundle\BusBundle\Soap\Action;

use Autobus\Bundle\BusBundle\Model\JobInterface;

/**
 * Interface SoapActionInterface
 *
 * @package Autobus\Bundle\BusBundle\Soap\Action
 */
interface SoapActionInterface
{
    /**
     * @param JobInterface $job
     *
     * @return void
     */
    public function setJob(JobInterface $job);
}
