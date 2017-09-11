<?php

namespace Autobus\Bundle\BusBundle;

use Autobus\Bundle\BusBundle\DependencyInjection\Compiler\RunnerCompilerPass;
use Autobus\Bundle\BusBundle\DependencyInjection\Compiler\SecurityStrategyCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AutobusBusBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RunnerCompilerPass());
        $container->addCompilerPass(new SecurityStrategyCompilerPass());
    }
}
