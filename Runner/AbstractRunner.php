<?php

namespace Autobus\Bundle\BusBundle\Runner;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Event\RunnerEvents;
use Autobus\Bundle\BusBundle\Event\RunnerHandleEvent;
use Autobus\Bundle\BusBundle\Event\RunnerHandleExceptionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class AbstractRunner
 */
abstract class AbstractRunner implements RunnerInterface
{
    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Context     $context
     * @param Job         $job
     * @param Execution   $execution
     *
     * @return mixed
     */
    abstract protected function process(Context $context, Job $job, Execution $execution);

    /**
     * @param Context   $context
     * @param Job       $job
     * @param Execution $execution
     *
     * @return Context
     */
    public function handle(Context $context, Job $job, Execution $execution)
    {
        $event = new RunnerHandleEvent($this, $context, $job, $execution);

        try {
            $this->eventDispatcher->dispatch(RunnerEvents::BEFORE_HANDLE, $event);

            $this->process($context, $job, $execution);
            $execution->setState($execution::STATE_SUCCESS);
            $this->eventDispatcher->dispatch(RunnerEvents::SUCCESS, $event);
        } catch (\Exception $exception) {
            $this->eventDispatcher->dispatch(
                RunnerEvents::ERROR,
                new RunnerHandleExceptionEvent(
                    $this,
                    $context,
                    $job,
                    $execution,
                    $exception
                )
            );
        }
        $this->eventDispatcher->dispatch(RunnerEvents::AFTER_HANDLE, $event);
    }
}
