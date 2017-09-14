<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Autobus\Bundle\BusBundle\Context;
use \Autobus\Bundle\BusBundle\Model\Job as BaseJob;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Service
 *
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\JobRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\HasLifecycleCallbacks()
 */
abstract class Job extends BaseJob
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
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     *
     * @ORM\Column(type="string", length=255)
     */
    protected $runner;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $config;

    /**
     * @var array
     */
    protected $configArray;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $trace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="Autobus\Bundle\BusBundle\Entity\Execution", mappedBy="job")
     */
    protected $executions;

    /**
     * @var Execution
     */
    protected $lastExecution;

    /**
     * @var JobGroup
     *
     * @ORM\ManyToOne(targetEntity="Autobus\Bundle\BusBundle\Entity\JobGroup", inversedBy="jobs")
     */
    protected $group;

    public function __construct()
    {
        $this->executions = new ArrayCollection();
        $this->secure = false;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps()
    {
        $this->setUpdatedAt(new \DateTime('now'));

        if ($this->getCreatedAt() == null) {
            $this->setCreatedAt(new \DateTime('now'));
        }
    }

    public function populateExecution(Execution $execution, Context $context)
    {
        $execution->setDate(new \DateTime());
        $execution->setCaller(gethostname());
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Job
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set service
     *
     * @param string $runner
     *
     * @return Job
     */
    public function setRunner($runner)
    {
        $this->runner = $runner;

        return $this;
    }

    /**
     * Get service
     *
     * @return string
     */
    public function getRunner()
    {
        return $this->runner;
    }

    /**
     * @param string $config JSON config
     *
     * @return Job
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * * @return array
     */
    public function getConfigArray()
    {
        if (null === $this->configArray) {
            $this->configArray = json_decode($this->config, true);
        }

        return $this->configArray;
    }

    /**
     * @param array $config
     *
     * @return Job
     */
    public function setConfigArray($config)
    {
        $this->configArray = $config;
        $this->config = json_encode($config);

        return $this;
    }

    /**
     * @param boolean $trace
     *
     * @return Job
     */
    public function setTrace($trace)
    {
        $this->trace = $trace;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getTrace()
    {
        return $this->trace;
    }

    /**
     * @param mixed $executions
     *
     * @return Job
     */
    public function setExecutions($executions)
    {
        $this->executions = $executions;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExecutions()
    {
        return $this->executions;
    }

    /**
     * @param mixed $createdAt
     *
     * @return Job
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return Job
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param mixed $group
     * @return Job
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get job type
     *
     * @return string
     */
    public function getType()
    {
        $klass = get_class($this);
        $typePos = strrpos($klass, '\\');
        return strtolower(substr($klass, $typePos + 1, -3)); // -3 is to remove 'Job' at the end
    }

    /**
     * @return string
     */
    public function getState()
    {
        return 'success';
    }

    /**
     * Get last execution
     *
     * @return Execution
     */
    public function getLastExecution()
    {
        if ($this->lastExecution === null) {
            $criteria = Criteria::create()
                ->orderBy(['date' => Criteria::DESC])
                ->setMaxResults(1);

            $lastExecution = $this->getExecutions()->matching($criteria)->first();
            $this->lastExecution = $lastExecution ? $lastExecution : null;
        }

        return $this->lastExecution;
    }
}
