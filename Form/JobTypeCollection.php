<?php

namespace Autobus\Bundle\BusBundle\Form;

class JobTypeCollection
{
    /**
     * @var JobTypeInterface[]
     */
    private $jobTypes;

    /**
     * JobTypeCollection constructor.
     * @param iterable $jobTypes
     */
    public function __construct(iterable $jobTypes)
    {
        foreach ($jobTypes as $jobType) {
            $this->jobTypes[] = $jobType;
        }
    }

    /**
     * @param string $type
     *
     * @return JobTypeInterface
     *
     * @throws \Exception
     */
    public function getJobType(string $type): JobTypeInterface
    {
        foreach ($this->jobTypes as $jobType) {
            if ($jobType->supports($type)) {
                return $jobType;
            }
        }

        throw new \Exception(sprintf('No JobType found for [\'%s\'] type', $type));
    }
}
