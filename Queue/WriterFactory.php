<?php

namespace Autobus\Bundle\BusBundle\Queue;

/**
 * Writer collection
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Queue
 */
class WriterFactory
{
    /**
     * @var WriterCollection
     */
    protected $writerCollection;

    /**
     * @param WriterCollection $writerCollection
     */
    public function __construct(WriterCollection $writerCollection)
    {
        $this->writerCollection = $writerCollection;
    }

    /**
     * Create writer by $type
     *
     * @param string $type
     *
     * @return WriterInterface
     *
     * @throws \Exception
     */
    public function create(string $type)
    {
        return $this->writerCollection->getWriter($type);
    }
}
