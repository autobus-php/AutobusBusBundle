<?php

namespace Autobus\Bundle\BusBundle\Form;

use Autobus\Bundle\BusBundle\Entity\Job;
use Autobus\Bundle\BusBundle\Runner\RunnerInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Job type factory
 */
class JobTypeFactory
{
    /**
     * @var RunnerInterface[]
     */
    protected $runners;

    /**
     * JobTypeFactory constructor.
     *
     * @param iterable $runners
     */
    public function __construct(iterable $runners)
    {
        $this->runners   = $runners;
    }

    /**
     * Create form type instance
     *
     * @param Job $job
     *
     * @return JobType
     * @throws \Exception
     */
    public function create(Job $job)
    {
        $type      = $job->getType();
        $className = '\\Autobus\Bundle\BusBundle\\Form\\' . ucfirst(strtolower($type)) . 'JobType';

        if (!class_exists($className)) {
            throw new \Exception(sprintf('%s does not exist', $className));
        }

        return new $className;
    }

    /**
     * @return RunnerInterface[]
     */
    public function getRunners()
    {
        return $this->runners;
    }

    /**
     * @param RunnerInterface[] $runners
     *
     * @return JobTypeFactory
     */
    public function setRunners($runners)
    {
        $this->runners = $runners;

        return $this;
    }
}
