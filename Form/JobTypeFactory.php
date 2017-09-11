<?php

namespace Autobus\Bundle\BusBundle\Form;

use Autobus\Bundle\BusBundle\Entity\Job;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Job type factory
 */
class JobTypeFactory
{
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
        $type = $job->getType();
        $className = '\\Autobus\Bundle\BusBundle\\Form\\'.ucfirst(strtolower($type)).'JobType';

        if (!class_exists($className)) {
            throw new \Exception(sprintf('%s does not exist', $className));
        }

        return new $className;
    }
}
