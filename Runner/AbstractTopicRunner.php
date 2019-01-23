<?php

namespace Autobus\Bundle\BusBundle\Runner;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Repository\TopicJobRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Interop\Queue\Message;
use Interop\Queue\Context as EnqueueContext;
use Interop\Queue\Processor;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * AbstractTopicRunner
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Runner
 */
abstract class AbstractTopicRunner extends AbstractRunner implements Processor
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * TopicRunner constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     * @param EntityManager            $entityManager
     * @param LoggerInterface          $logger
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, EntityManager $entityManager, LoggerInterface $logger)
    {
        parent::__construct($eventDispatcher);
        $this->entityManager = $entityManager;
        $this->logger        = $logger;
    }

    /**
     * Process messages from given topics
     *
     * @param Message        $message
     * @param EnqueueContext $session
     *
     * @return object|string
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function process(Message $message, EnqueueContext $session)
    {
        try {
            /** @var TopicJobRepository $topicJobRepository */
            $topicJobRepository = $this->entityManager->getRepository('AutobusBusBundle:TopicJob');
            $jobs               = $topicJobRepository->getByTopics($this->getTopics());
            foreach ($jobs as $job) {
                $execution = new Execution();
                $context   = $this->prepareContext($message);
                $this->handle($context, $job, $execution);
                $this->entityManager->persist($execution);
                $this->entityManager->persist($job);
            }
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error(sprintf(
                'Error during topics reading (%s) with message: %s',
                implode(', ', $this->getTopics()),
                $exception->getMessage()
            ));

            return self::REJECT;
        }

        return self::ACK;
    }

    /**
     * Get topics
     *
     * @return array
     */
    abstract public function getTopics();

    /**
     * Prepare a new context instance
     *
     * @param Message $message
     *
     * @return Context
     */
    protected function prepareContext(Message $message)
    {
        $context = new Context();
        $context->setRequest(new Request());
        $context->setResponse(new Response());
        $context->setMessage($message->getBody());

        return $context;
    }
}