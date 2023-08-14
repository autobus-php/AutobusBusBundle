<?php

namespace Autobus\Bundle\BusBundle\Queue;

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
        // TODO : add write logic
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
