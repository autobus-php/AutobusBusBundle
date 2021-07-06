<?php

namespace Autobus\Bundle\BusBundle\Runner;

class RunnerCollection
{
    /**
     * @var RunnerInterface[]
     */
    private $runners = [];

    /**
     * RunnerCollection constructor.
     *
     * @param iterable $runners
     */
    public function __construct(iterable $runners)
    {
        foreach ($runners as $runner) {
            $this->runners[] = $runner;
        }
    }

    /**
     * @param string $type
     *
     * @return RunnerInterface[]
     */
    public function getRunners(string $type): array
    {
        $runners = [];
        foreach ($this->runners as $runner) {
            if ($runner->supports($type)) {
                $runners[] = $runner;
            }
        }

        return $runners;
    }
}
