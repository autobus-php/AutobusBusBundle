<?php

namespace Autobus\Bundle\BusBundle\Event;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Runner\RunnerInterface;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\Execution;

/**
 * Class RunnerHandleExceptionEvent
 */
class RunnerHandleExceptionEvent extends RunnerHandleEvent
{
    /**
     * @var \Exception
     */
    protected $exception;

    /**
     * @param RunnerInterface $runner
     * @param Context         $context
     * @param Job             $job
     * @param Execution       $execution
     * @param \Exception      $exception
     */
    public function __construct(
        RunnerInterface $runner,
        Context $context,
        Job $job,
        Execution $execution,
        \Exception $exception
    ) {
        parent::__construct($runner, $context, $job, $execution);

        $this->exception = $exception;
    }

    /**
     * @return \Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
