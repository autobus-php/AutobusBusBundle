<?php

namespace Autobus\Bundle\BusBundle\Queue;

use Autobus\Bundle\BusBundle\Helper\TopicHelper;
use Aws\Exception\AwsException;
use Aws\Sqs\SqsClient;

/**
 * AWS SQS writer
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Queue
 */
class SqsWriter implements WriterInterface
{
    /**
     * AWS SQS Version
     *
     * @var string
     */
    const SQS_VERSION = '2012-11-05';

    /**
     * @var  SqsClient
     */
    protected $sqsClient;

    /**
     * @var TopicHelper
     */
    protected $topicHelper;

    /**
     * Writer constructor.
     *
     * @param TopicHelper $topicHelper
     */
    public function __construct(TopicHelper $topicHelper)
    {
        $this->topicHelper = $topicHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function write($topicName, $message)
    {
        $this->sqsClient = new SqsClient([
            'region'  => getenv('AWS_REGION'),
            'version' => self::SQS_VERSION
        ]);
        $queueName       = $this->topicHelper->getRealTopicName($topicName);

        // Create / get queue
        $queueUrl = $this->getQueueUrlByName($queueName);
        if ($queueUrl === null) {
            $queueUrl = $this->createQueue($queueName);
        }

        // Write message to queue
        $this->writeMessage($queueUrl, $message);
    }

    /**
     * Write $data array to given $topic
     *
     * @param string $topicName
     * @param array  $data
     *
     * @return void
     */
    public function writeAsArray($topicName, $data)
    {
        $this->write($topicName, json_encode($data));
    }

    /**
     * Get queue url by $queueName
     *
     * @param string $queueName
     *
     * @return string | null
     */
    protected function getQueueUrlByName($queueName)
    {
        try {
            $result = $this->sqsClient->getQueueUrl([
                'QueueName' => $queueName,
            ]);
            return $result->get('QueueUrl');
        } catch (AwsException $e) {
            return null;
        }
    }

    /**
     * Create queue and return url
     *
     * @param string $queueName
     *
     * @return string | null
     */
    protected function createQueue($queueName)
    {
        try {
            $result = $this->sqsClient->createQueue([
                'QueueName' => $queueName,
            ]);
            return $result->get('QueueUrl');
        } catch (AwsException $e) {
            return null;
        }
    }

    /**
     * Write $message in $queueUrl
     *
     * @param string $queueUrl
     * @param string $message
     *
     * @return bool
     */
    protected function writeMessage($queueUrl, $message)
    {
        try {
            $this->sqsClient->sendMessage([
                'QueueUrl' => $queueUrl,
                'MessageBody' => $message
            ]);
            return true;
        } catch (AwsException $e) {
            return false;
        }
    }
}
