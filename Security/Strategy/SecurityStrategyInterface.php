<?php

namespace Autobus\Bundle\BusBundle\Security\Strategy;

use Symfony\Component\HttpFoundation\Request;

/**
 * Interface SecurityStrategyInterface
 */
interface SecurityStrategyInterface
{
    /**
     * @return string
     */
    public function getMode();

    /**
     * @param Request $request
     * @param array   $config
     *
     * @return void
     */
    public function check(Request $request, array $config);
}
