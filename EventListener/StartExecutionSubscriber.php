<?php

namespace Autobus\Bundle\BusBundle\EventListener;

use Autobus\Bundle\BusBundle\Entity\CronJob;
use Autobus\Bundle\BusBundle\Entity\QueueJob;
use Autobus\Bundle\BusBundle\Entity\WebJob;
use Autobus\Bundle\BusBundle\Event\RunnerEvents;
use Autobus\Bundle\BusBundle\Event\RunnerHandleEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class StartExecutionSubscriber
 */
class StartExecutionSubscriber implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            RunnerEvents::BEFORE_HANDLE => array('onBeforeHandle', 128)
        );
    }

    /**
     * @param RunnerHandleEvent $event
     */
    public function onBeforeHandle(RunnerHandleEvent $event)
    {
        $execution = $event->getExecution();
        $request   = $event->getRequest();
        $job       = $event->getJob();

        $job->populateExecution($execution, $event->getContext());

        $execution
            ->setJob($job)
            ->start();

        if ($job instanceof WebJob) {
            $allowedMethods = $job->getMethods(); // ?
            if (!empty($allowedMethods) && !in_array($request->getMethod(), $allowedMethods)) {
                throw new BadRequestHttpException(
                    sprintf(
                        'Method [%s] not allowed (allowed methods: %s]',
                        $request->getMethod(),
                        implode(', ', $allowedMethods)
                    )
                );
            }
        }
    }
}
