<?php

namespace Autobus\Bundle\BusBundle\Security;

use Autobus\Bundle\BusBundle\Security\Strategy\SecurityStrategyInterface;
use Symfony\Component\HttpFoundation\Request;
use Autobus\Bundle\BusBundle\Security\Strategy\Exception\UnknownSecurityStrategyException;

/**
 * Class SecurityChain
 */
class SecurityChain
{
    /**
     * @var array
     */
    protected $securityStrategies = array();

    /**
     * @param SecurityStrategyInterface $strategy
     */
    public function addSecurityStrategy(SecurityStrategyInterface $strategy)
    {
        $this->securityStrategies[$strategy->getMode()] = $strategy;
    }

    /**
     * @param Request $request
     * @param array   $modes
     */
    public function check(Request $request, array $modes)
    {
        foreach ($modes as $strategyConfig) {
            if (!isset($this->securityStrategies[$strategyConfig['mode']])) {
                throw new UnknownSecurityStrategyException(
                    sprintf(
                        'security strategy %s does not exist',
                        $strategyConfig['mode']
                    )
                );
            }

            /** @var SecurityStrategyInterface $strategy */
            $strategy = $this->securityStrategies[$strategyConfig['mode']];
            $strategy->check($request, !empty($strategyConfig['config']) ? $strategyConfig['config'] : []);
        }
    }
}
