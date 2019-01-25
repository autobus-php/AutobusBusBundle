<?php

namespace Autobus\Bundle\BusBundle\DependencyInjection\Compiler;

use Autobus\Bundle\BusBundle\Runner\RunnerChain;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class RunnerCompilerPass
 */
class RunnerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(RunnerChain::class)) {
            return;
        }

        $definition     = $container->findDefinition(RunnerChain::class);
        $taggedServices = $container->findTaggedServiceIds('bus.runner');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addRunner', [new Reference($id), $id, $attributes['label']]);

                // Set service as public
                $serviceDefinition = $container->getDefinition($id);
                $serviceDefinition->setPublic(true)->setPrivate(false);
            }
        }
    }
}
