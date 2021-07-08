<?php

namespace Autobus\Bundle\BusBundle\Runner;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Event\RunnerEvents;
use Autobus\Bundle\BusBundle\Event\RunnerHandleEvent;
use Autobus\Bundle\BusBundle\Event\RunnerHandleExceptionEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @param Context   $context
     * @param Job       $job
     * @param Execution $execution
     *
     * @return mixed
     */
    abstract protected function run(Context $context, Job $job, Execution $execution);

    /**
     * @param Context   $context
     * @param Job       $job
     * @param Execution $execution
     *
     * @return Context
     */
    public function handle(Context $context, Job $job, Execution $execution)
    {
        $context = $this->prepareContext($context);
        $event   = new RunnerHandleEvent($this, $context, $job, $execution);

        try {
            $this->eventDispatcher->dispatch(RunnerEvents::BEFORE_HANDLE, $event);

            // Validate job configuration
            if (!$this->validateConfig($job)) {
                throw new \Exception('Invalid job configuration.');
            }

            // And run
            $this->run($context, $job, $execution);
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

    /**
     * Prepare context with request and response if necessary
     *
     * @param Context $context
     *
     * @return Context
     */
    protected function prepareContext(Context $context)
    {
        if ($context->getRequest() === null) {
            $context->setRequest(new Request());
        }
        if ($context->getResponse() === null) {
            $context->setResponse(new Response());
        }

        return $context;
    }

    /**
     * Validate config for $job
     *
     * @param Job $job
     *
     * @return bool
     */
    protected function validateConfig(Job $job)
    {
        // TODO: Implement validateConfig() method.

        return true;
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        throw new \LogicException(__METHOD__ . ' : "supports" function should be implemented in child classes.');
    }
}
