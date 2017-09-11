<?php

namespace Autobus\Bundle\BusBundle\Security\Strategy;

use Symfony\Component\HttpFoundation\IpUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class IPWhiteListStrategy
 */
class IPWhiteListStrategy extends AbstractSecurityStrategy
{
    protected $mode = 'ip_white_list';

    /**
     * @param Request $request
     * @param array   $config
     *
     * @return void
     */
    public function check(Request $request, array $config)
    {
        $clientIp = $request->getClientIp();
        $allowedIps = $config['ips'];

        if (!IpUtils::checkIp($clientIp, $allowedIps)) {
            throw new AuthenticationException(sprintf('Unauthorized IP address [%s]', $clientIp));
        }
    }
}
