<?php

namespace Autobus\Bundle\BusBundle\Helper;

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
}
