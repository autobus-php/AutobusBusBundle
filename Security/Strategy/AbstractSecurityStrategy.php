<?php

namespace Autobus\Bundle\BusBundle\Security\Strategy;

/**
 * Class AbstractSecurityStrategy
 */
abstract class AbstractSecurityStrategy implements SecurityStrategyInterface
{
    /**
     * @var string
     */
    protected $mode;

    /**
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
}
