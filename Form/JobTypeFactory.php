<?php

namespace Autobus\Bundle\BusBundle\Form;

use Autobus\Bundle\BusBundle\Entity\Job;

/**
 * Job type factory
 */
class JobTypeFactory
{
    /**
     * @var JobTypeCollection
     */
    protected $jobTypeCollection;

    /**
     * JobTypeFactory constructor.
     *
     * @param JobTypeCollection $jobTypeCollection
     */
    public function __construct(JobTypeCollection $jobTypeCollection)
    {
        $this->jobTypeCollection = $jobTypeCollection;
    }

    /**
     * Create form type instance
     *
     * @param Job $job
     *
     * @return JobTypeInterface
     *
     * @throws \Exception
     */
    public function create(Job $job)
    {
        $type = $job->getType();

        return $this->jobTypeCollection->getJobType($type);
    }
}
