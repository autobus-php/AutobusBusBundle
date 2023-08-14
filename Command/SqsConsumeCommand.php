<?php

namespace Autobus\Bundle\BusBundle\Command;

use Autobus\Bundle\BusBundle\Entity\TopicJob;
use Autobus\Bundle\BusBundle\Helper\TopicHelper;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Consume AWS SQS messages for current register topics
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Command
 */
class SqsConsumeCommand extends Command
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
     * @var TopicHelper
     */
    protected $topicHelper;

    /**
     * QueueConsumeCommand constructor.
     *
     * @param string|null            $name
     * @param ContainerInterface     $container
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $entityManager
     * @param TopicHelper            $topicHelper
     */
    public function __construct(string $name = null, ContainerInterface $container, LoggerInterface $logger, EntityManagerInterface $entityManager, TopicHelper $topicHelper)
    {
        parent::__construct($name);
        $this->container     = $container;
        $this->logger        = $logger;
        $this->entityManager = $entityManager;
        $this->topicHelper   = $topicHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('autobus:sqs:consume')
            ->setDescription('Consume AWS SQS messages for current register topics')
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
        $wait         = $input->getOption('wait');

        while (1) {
            /** @var TopicJob[] $topicJobs */
            $topicJobs = $this->entityManager->getRepository('AutobusBusBundle:TopicJob')->findAll();

            // Pull messages for each job
            foreach ($topicJobs as $topicJob) {
                $topicName     = $topicJob->getTopic();
                $realTopicName = $subscriptionName = $this->topicHelper->getRealTopicName($topicName);
                // TODO : pull messages here
            }
            sleep($wait);
        }
    }
}
