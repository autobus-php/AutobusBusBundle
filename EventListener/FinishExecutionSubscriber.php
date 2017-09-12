<?php

namespace Autobus\Bundle\BusBundle\EventListener;

use Autobus\Bundle\BusBundle\Entity\WebJob;
use Autobus\Bundle\BusBundle\Event\RunnerEvents;
use Autobus\Bundle\BusBundle\Event\RunnerHandleEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class FinishExecutionSubscriber
 */
class FinishExecutionSubscriber implements EventSubscriberInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

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
             RunnerEvents::AFTER_HANDLE => 'onAfterHandle'
        );
    }

    public function onAfterHandle(RunnerHandleEvent $event)
    {
        $execution = $event->getExecution();
        $response  = $event->getResponse();
        $job       = $event->getJob();
        $request   = $event->getRequest();
        $context   = $event->getContext();

        $execution->finish();

        if ($job instanceof WebJob) {
            if ($response->getStatusCode() >= 400) {
                $execution->setState($execution::STATE_ERROR);
            }

            $response->setContent($context->getMessage());

            if ($job->getTrace()) {
                $logs = $this->logger->getLogs();
                $logs = array_map(function ($log) {
                    return sprintf('%s [%s] %s', $log['timestamp'], $log['priorityName'], $log['message']);
                }, $logs);
                $execution->setLogs(implode("\n", $logs));

                $requestString = $request->headers->__toString();
                $requestString .= "\n\n".$request->getContent();
                $execution->setRequest($requestString);

                $responseString = sprintf("HTTP %d\n\n", $response->getStatusCode());
                $responseString .= $response->headers->__toString();
                $responseString .= "\n\n".$response->getContent();
                $execution->setResponse($responseString);
            }

            if ($request->getContentType() == 'xml') {
                $response->setContent('<result><![CDATA['.$response->getContent().']]></result>');
            } elseif ($request->getContentType() == 'json') {
                $response->setContent(sprintf('{"result":"%s"}', addslashes($response->getContent())));
            }
        }
    }
}
