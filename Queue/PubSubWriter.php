<?php

namespace Autobus\Bundle\BusBundle\Queue;

use Google\Cloud\PubSub\PubSubClient;
use Autobus\Bundle\BusBundle\Helper\PubSubHelper;

/**
 * Google PubSub writer
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Queue
 */
class PubSubWriter implements WriterInterface
{
    /**
     * @var PubSubHelper
     */
    protected $pubSubHelper;

    /**
     * Writer constructor.
     *
     * @param PubSubHelper $pubSubHelper
     */
    public function __construct(PubSubHelper $pubSubHelper)
    {
        $this->pubSubHelper = $pubSubHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function write($topicName, $message)
    {
        $pubSubClient  = new PubSubClient();
        $realTopicName = $this->pubSubHelper->getRealTopicName($topicName);
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
}
