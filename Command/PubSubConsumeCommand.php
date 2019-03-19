<?php

namespace Autobus\Bundle\BusBundle\Command;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Entity\TopicJob;
use Autobus\Bundle\BusBundle\Helper\PubSubHelper;
use Autobus\Bundle\BusBundle\Runner\AbstractTopicRunner;
use Doctrine\ORM\EntityManagerInterface;
use Google\Cloud\PubSub\Message;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Google\Cloud\PubSub\PubSubClient;

/**
 * Consume Google PubSub messages for current register topics
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Command
 */
class PubSubConsumeCommand extends Command
{
    /**
     * Run pull command every 10 seconds
     *
     * @var int
     */
    const DEFAULT_WAIT = 10;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var PubSubHelper
     */
    protected $pubSubHelper;

    /**
     * QueueConsumeCommand constructor.
     *
     * @param string|null            $name
     * @param ContainerInterface     $container
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $entityManager
     * @param PubSubHelper           $pubSubHelper
     */
    public function __construct(string $name = null, ContainerInterface $container, LoggerInterface $logger, EntityManagerInterface $entityManager, PubSubHelper $pubSubHelper)
    {
        parent::__construct($name);
        $this->container     = $container;
        $this->logger        = $logger;
        $this->entityManager = $entityManager;
        $this->pubSubHelper  = $pubSubHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('autobus:pubsub:consume')
            ->setDescription('Consume Google PubSub messages for current register topics')
            ->addOption('wait', 'w', InputOption::VALUE_OPTIONAL, 'Sleep time in seconds between each pull command', self::DEFAULT_WAIT);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pubSubClient = new PubSubClient();
        $wait         = $input->getOption('wait');

        while (1) {
            /** @var TopicJob[] $topicJobs */
            $topicJobs = $this->entityManager->getRepository('AutobusBusBundle:TopicJob')->findAll();

            // Pull messages for each job
            foreach ($topicJobs as $topicJob) {
                $topicName     = $topicJob->getTopic();
                $realTopicName = $subscriptionName = $this->pubSubHelper->getRealTopicName($topicName);
                $subscription  = $pubSubClient->subscription($subscriptionName);
                if (!$subscription->exists()) {
                    // Check topic
                    $topic = $pubSubClient->topic($realTopicName);
                    if (!$topic->exists()) {
                        $this->logger->warning(sprintf('[%s] No topic with name %s for subscription', __METHOD__, $realTopicName));
                        continue;
                    }

                    // Create subscription
                    $subscription = $pubSubClient->subscribe($subscriptionName, $realTopicName);
                }

                // Process messages
                $messages = $subscription->pull();
                if (!empty($messages)) {
                    foreach ($messages as $message) {
                        if ($this->processMessage($topicJob, $message)) {
                            $subscription->acknowledge($message);
                        }
                    }
                }
            }
            sleep($wait);
        }
    }

    /**
     * Process pubsub message
     *
     * @param TopicJob $topicJob
     * @param Message  $message
     *
     * @return bool
     */
    protected function processMessage(TopicJob $topicJob, Message $message)
    {
        try {
            // Get runner
            /** @var AbstractTopicRunner $runner */
            $runner = $this->container->get($topicJob->getRunner());

            // Process message
            $execution = new Execution();
            $context   = new Context();
            $context->setMessage($message->data());
            $runner->handle($context, $topicJob, $execution);
            $this->entityManager->persist($execution);
            $this->entityManager->persist($topicJob);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error(sprintf(
                    '[%s] Error during execution creation for topicjob %s with message : %s',
                    __METHOD__,
                    $topicJob->getName(),
                    $exception->getMessage()
                )
            );

            return false;
        }

        return true;
    }
}
