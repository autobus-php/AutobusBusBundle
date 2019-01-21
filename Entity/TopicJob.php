<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Job used to read data from AQMP queue
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Entity
 *
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\TopicJobRepository")
 */
class TopicJob extends Job
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     *
     * @ORM\Column(type="string")
     */
    protected $topic;

    /**
     * @return string
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param string $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }
}
