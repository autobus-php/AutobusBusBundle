<?php

namespace Autobus\Bundle\BusBundle\DependencyInjection\Compiler;

use Autobus\Bundle\BusBundle\Security\SecurityChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SecurityStrategyCompilerPass
 */
class SecurityStrategyCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(SecurityChain::class)) {
            return;
        }

        $definition = $container->findDefinition(SecurityChain::class);

        $taggedServices = $container->findTaggedServiceIds('bus.security_strategy');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addSecurityStrategy', array(new Reference($id), $id));
        }
    }
}
