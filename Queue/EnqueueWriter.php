<?php

namespace Autobus\Bundle\BusBundle\Queue;

use Enqueue\Client\Message;
use Enqueue\Client\TraceableProducer as Producer;

/**
 * Topic queue writer
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Queue
 */
class EnqueueWriter implements WriterInterface
{
    /**
     * @var Producer
     */
    protected $producer;

    /**
     * Writer constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    /**
     * {@inheritdoc}
     */
    public function write($topic, $message)
    {
        $this->producer->sendEvent($topic, $message);
    }

    /**
     * Write $data array to given $topic
     *
     * @param string $topic
     * @param array  $data
     *
     * @return void
     */
    public function writeAsArray($topic, $data)
    {
        $message = new Message(json_encode($data));
        $message->setContentType('application/json');

        $this->write($topic, $message);
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function supports(string $type): bool
    {
        return $type === 'amqps:';
    }
}
