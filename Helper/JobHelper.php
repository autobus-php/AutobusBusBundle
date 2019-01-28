<?php

namespace Autobus\Bundle\BusBundle\Helper;

use Autobus\Bundle\BusBundle\Entity\Job;
use Symfony\Component\HttpKernel\Kernel;

/**
 * JobHelper
 *
 * @author  Simon CARRE <simon.carre@clickandmortar.fr>
 * @package Autobus\Bundle\BusBundle\Helper
 */
class JobHelper
{
    /**
     * Project dir placeholder
     *
     * @var string
     */
    const CONFIG_PLACEHOLDER_PROJECT_DIR = '%projectDir%';

    /**
     * Wsdl path key in job config array
     *
     * @var string
     */
    const CONFIG_WSDL_PATH = 'wsdlPath';

    /**
     * @var Kernel
     */
    protected $kernel;

    /**
     * JobHelper constructor.
     *
     * @param Kernel $kernel
     */
    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Get real root path by replacing project placeholder
     *
     * @param string $path
     *
     * @return string | null
     */
    public function getPathFromRoot($path)
    {
        return str_replace(self::CONFIG_PLACEHOLDER_PROJECT_DIR, $this->kernel->getProjectDir(), $path);
    }

    /**
     * Load WsdlPath from job parameter
     *
     * @param Job $job
     *
     * @return string|null
     */
    public function loadWsdlPath(Job $job)
    {
        // Check for wsdl path in job configuration
        $config = $job->getConfigArray();
        if (array_key_exists(self::CONFIG_WSDL_PATH, $config)) {
            return $config[self::CONFIG_WSDL_PATH];
        }

        return null;
    }
}
