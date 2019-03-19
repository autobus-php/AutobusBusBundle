<?php

namespace Autobus\Bundle\BusBundle\Helper;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * PubSubHelper
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Helper
 */
class PubSubHelper
{
    /**
     * @var KernelInterface
     */
    protected $kernel;

    /**
     * PubSubHelper constructor.
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Get real topic name in Google PubSub by adding env as suffix
     *
     * @param string $topicName
     *
     * @return string
     */
    public function getRealTopicName(string $topicName)
    {
        return sprintf(
            '%s_%s',
            $topicName,
            $this->kernel->getEnvironment()
        );
    }
}
