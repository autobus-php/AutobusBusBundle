<?php

namespace Autobus\Bundle\BusBundle\Queue;

use Google\Cloud\PubSub\PubSubClient;

/**
 * Topic queue writer
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Queue
 */
class Writer implements WriterInterface
{
    /**
     * {@inheritdoc}
     */
    public function write($topicName, $message)
    {
        $pubSubClient = new PubSubClient();
        $topic        = $pubSubClient->topic($topicName);
        if (!$topic->exists()) {
            $topic = $pubSubClient->createTopic($topicName);
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
