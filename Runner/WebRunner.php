<?php

namespace Autobus\Bundle\BusBundle\Runner;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\Execution;

class WebRunner extends AbstractRunner
{
    protected function run(Context $context, Job $job, Execution $execution)
    {
        // TODO: Implement run() method.
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        throw new \LogicException(__METHOD__ . ' : Supports function should be implemented in child classes.');
    }
}
