<?php

namespace Autobus\Bundle\BusBundle\Runner;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\Execution;

interface RunnerInterface
{
    /**
     * @param Context   $context
     * @param Job       $job
     * @param Execution $execution
     *
     * @return Context
     */
    public function handle(Context $context, Job $job, Execution $execution);

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool;
}
