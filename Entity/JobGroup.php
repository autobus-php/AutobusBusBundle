<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Autobus\Bundle\BusBundle\Model\JobGroup as BaseJobGroup;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * JobGroup
 *
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\JobGroupRepository")
 */
class JobGroup extends BaseJobGroup
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    protected $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Autobus\Bundle\BusBundle\Entity\Job", mappedBy="group")
     */
    protected $jobs;
}
