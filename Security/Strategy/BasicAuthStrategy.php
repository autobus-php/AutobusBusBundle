<?php

namespace Autobus\Bundle\BusBundle\Security\Strategy;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class BasicAuthStrategy
 */
class BasicAuthStrategy extends AbstractSecurityStrategy
{
    protected $mode = 'basic_auth';

    /**
     * @param Request $request
     * @param array   $config
     *
     * @return void
     */
    public function check(Request $request, array $config)
    {
        $requestUser = $request->getUser();
        $requestPass = $request->getPassword();
        $configUser = $config['username'];
        $configPass = $config['password'];
        if ($requestUser != $configUser || $requestPass != $configPass) {
            throw new AuthenticationException(sprintf('Invalid credentials for user [%s]', $requestUser));
        }
    }
}
