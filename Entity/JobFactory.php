<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Job factory
 */
class JobFactory
{
    /**
     * Create entity instance
     *
     * @param string $type
     *
     * @return Job
     * @throws \Exception
     */
    public function create($type)
    {
        $className = '\\Autobus\Bundle\BusBundle\\Entity\\'.ucfirst(strtolower($type)).'Job';

        if (!class_exists($className)) {
            throw new \Exception(sprintf('%s does not exist', $className));
        }

        return new $className;
    }
}
