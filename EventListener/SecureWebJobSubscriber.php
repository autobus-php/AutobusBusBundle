<?php

namespace Autobus\Bundle\BusBundle\EventListener;

use Autobus\Bundle\BusBundle\Entity\WebJob;
use Autobus\Bundle\BusBundle\Event\RunnerEvents;
use Autobus\Bundle\BusBundle\Event\RunnerHandleEvent;
use Autobus\Bundle\BusBundle\Security\SecurityChain;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class SecureWebJobSubscriber
 */
class SecureWebJobSubscriber implements EventSubscriberInterface
{
    /**
     * @var SecurityChain
     */
    private $securityChain;

    /**
     * @param SecurityChain $securityChain
     */
    public function __construct(SecurityChain $securityChain)
    {
        $this->securityChain = $securityChain;
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
            RunnerEvents::BEFORE_HANDLE => array('onBeforeHandle', 64)
        );
    }

    public function onBeforeHandle(RunnerHandleEvent $event)
    {
        $job     = $event->getJob();
        $request = $event->getRequest();

        $config = $job->getConfigArray();

        if ($job instanceof WebJob && $job->isSecure() && isset($config['security']['modes'])) {
            $this->securityChain->check($request, $config['security']['modes']);
        }
    }
}
