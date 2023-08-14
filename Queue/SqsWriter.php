<?php

namespace Autobus\Bundle\BusBundle\Queue;

use Autobus\Bundle\BusBundle\Helper\SqsHelper;
use Autobus\Bundle\BusBundle\Helper\TopicHelper;

/**
 * AWS SQS writer
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Queue
 */
class SqsWriter implements WriterInterface
{
    /**
     * @var TopicHelper
     */
    protected $topicHelper;

    /**
     * @var  SqsHelper
     */
    protected $sqsHelper;

    /**
     * Writer constructor.
     *
     * @param TopicHelper $topicHelper
     * @param SqsHelper   $sqsHelper
     */
    public function __construct(TopicHelper $topicHelper, SqsHelper $sqsHelper)
    {
        $this->topicHelper = $topicHelper;
        $this->sqsHelper   = $sqsHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function write($topicName, $message)
    {
        $queueName       = $this->topicHelper->getRealTopicName($topicName);

        // Create / get queue
        $queueUrl = $this->sqsHelper->getQueueUrlByName($queueName);
        if ($queueUrl === null) {
            $queueUrl = $this->sqsHelper->createQueue($queueName);
        }

        // Write message to queue
        $this->sqsHelper->writeMessage($queueUrl, $message);
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
}
