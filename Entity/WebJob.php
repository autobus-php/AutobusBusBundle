<?php

namespace Autobus\Bundle\BusBundle\Entity;

use Autobus\Bundle\BusBundle\Context;
use Autobus\Bundle\BusBundle\Model\WebJobInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * WebService
 *
 * @ORM\Entity(repositoryClass="Autobus\Bundle\BusBundle\Repository\WebJobRepository")
 * @UniqueEntity("path")
 */
class WebJob extends Job implements WebJobInterface
{
    /**
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $methods;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $secure;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=150)
     *
     * @ORM\Column(type="string", unique=true, length=150)
     */
    protected $path;

    public function __construct()
    {
        parent::__construct();

        $this->methods = [];
    }

    public function populateExecution(Execution $execution, Context $context)
    {
        parent::populateExecution($execution, $context);

        $execution->setCaller($context->getRequest()->getClientIp());
    }

    /**
     * @param array $methods
     *
     * @return WebJob
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;

        return $this;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param boolean $secure
     *
     * @return WebJob
     */
    public function setSecure($secure)
    {
        $this->secure = $secure;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * @param string $path
     *
     * @return WebJob
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
