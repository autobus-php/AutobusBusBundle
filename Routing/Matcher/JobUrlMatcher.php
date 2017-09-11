<?php

namespace Autobus\Bundle\BusBundle\Routing\Matcher;

use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Route;

/**
 * Class JobUrlMatcher
 */
class JobUrlMatcher extends UrlMatcher
{
    /**
     * @param Route $route
     */
    public function addRoute(Route $route)
    {
        $this->routes->add($route->getPath(), $route);
    }
}
