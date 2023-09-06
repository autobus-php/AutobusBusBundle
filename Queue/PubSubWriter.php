<?php

namespace Autobus\Bundle\BusBundle\Queue;

use Google\Cloud\PubSub\PubSubClient;
use Autobus\Bundle\BusBundle\Helper\TopicHelper;

/**
 * Google PubSub writer
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Queue
 */
class PubSubWriter implements WriterInterface
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
        $pubSubClient  = new PubSubClient();
        $realTopicName = $this->topicHelper->getRealTopicName($topicName);
        $topic         = $pubSubClient->topic($realTopicName);
        if (!$topic->exists()) {
            $topic = $pubSubClient->createTopic($realTopicName);
        }

        $topic->publish([
            'data' => $message,
        ]);
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
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'gps:';
    }
}
