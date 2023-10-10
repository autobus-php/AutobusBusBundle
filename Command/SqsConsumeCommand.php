<?php

namespace Autobus\Bundle\BusBundle\Command;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Entity\Execution;
use Autobus\Bundle\BusBundle\Entity\TopicJob;
use Autobus\Bundle\BusBundle\Helper\SqsHelper;
use Autobus\Bundle\BusBundle\Helper\TopicHelper;
use Autobus\Bundle\BusBundle\Runner\AbstractTopicRunner;
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
     * Default limit for pulls
     *
     * @var int
     */
    const DEFAULT_LIMIT = -1;

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
     * @var SqsHelper
     */
    protected $sqsHelper;

    /**
     * QueueConsumeCommand constructor.
     *
     * @param string|null            $name
     * @param ContainerInterface     $container
     * @param LoggerInterface        $logger
     * @param EntityManagerInterface $entityManager
     * @param TopicHelper            $topicHelper
     * @param SqsHelper              $sqsHelper
     */
    public function __construct(
        string $name = null,
        ContainerInterface $container,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        TopicHelper $topicHelper,
        SqsHelper $sqsHelper
    )
    {
        parent::__construct($name);
        $this->container     = $container;
        $this->logger        = $logger;
        $this->entityManager = $entityManager;
        $this->topicHelper   = $topicHelper;
        $this->sqsHelper     = $sqsHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('autobus:sqs:consume')
            ->setDescription('Consume AWS SQS messages for current register topics')
            ->addOption('wait', 'w', InputOption::VALUE_OPTIONAL, 'Sleep time in seconds between each pull command', self::DEFAULT_WAIT)
            ->addOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Number of pulls before stopping command. -1 to disable', self::DEFAULT_LIMIT);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pullIndex = 0;
        $wait      = $input->getOption('wait');
        $limit     = intval($input->getOption('limit'));
        /** @var TopicJob[] $topicJobs */
        $topicJobs = $this->entityManager->getRepository('AutobusBusBundle:TopicJob')->findAll();

        while ($pullIndex < $limit || $limit === -1) {
            // Pull messages for each job
            foreach ($topicJobs as $topicJob) {
                $pullIndex++;
                $topicName     = $topicJob->getTopic();
                $realTopicName = $this->topicHelper->getRealTopicName($topicName);
                $queueUrl      = $this->sqsHelper->getQueueUrlByName($realTopicName);
                if ($queueUrl === null) {
                    $this->logger->warning(sprintf('[%s] No queue with name %s', __METHOD__, $realTopicName));
                    continue;
                }

                $messages = $this->sqsHelper->getMessages($queueUrl);
                foreach ($messages as $message) {
                    if ($this->processMessage($topicJob, $message)) {
                        $this->sqsHelper->deleteMessage($queueUrl, $message['ReceiptHandle']);
                    }
                }
            }
            sleep($wait);
        }
    }

    /**
     * Process sqs message
     *
     * @param TopicJob $topicJob
     * @param array    $message
     *
     * @return bool
     */
    protected function processMessage(TopicJob $topicJob, $message)
    {
        try {
            // Get runner
            /** @var AbstractTopicRunner $runner */
            $runner = $this->container->get($topicJob->getRunner());

            // Process message
            $execution = new Execution();
            $context   = new Context();
            $context->setMessage($message['Body']);
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
