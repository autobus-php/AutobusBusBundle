<?php

namespace Autobus\Bundle\BusBundle\Queue;

/**
 * Interface WriterInterface
 *
 * @package Autobus\Bundle\BusBundle\Queue
 */
interface WriterInterface
{
    /**
     * Write $message to given $topic
     *
     * @param string $topic
     * @param string $message
     *
     * @return void
     */
    public function write($topic, $message);
}
