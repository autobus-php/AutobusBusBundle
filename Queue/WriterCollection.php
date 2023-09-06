<?php

namespace Autobus\Bundle\BusBundle\Queue;

/**
 * Writer collection
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Queue
 */
class WriterCollection
{
    /**
     * @var WriterInterface[]
     */
    private $writers;

    /**
     * @param iterable $writers
     */
    public function __construct(iterable $writers)
    {
        foreach ($writers as $writer) {
            $this->writers[] = $writer;
        }
    }

    /**
     * @param string $type
     *
     * @return WriterInterface
     *
     * @throws \Exception
     */
    public function getWriter(string $type): WriterInterface
    {
        foreach ($this->writers as $writer) {
            if ($writer->supports($type)) {
                return $writer;
            }
        }

        throw new \Exception(sprintf('No writer found for [\'%s\'] type', $type));
    }
}
