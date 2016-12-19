<?php

namespace DavidKmenta\CommandSupervisorBundle;

use DavidKmenta\CommandSupervisorBundle\DependencyInjection\Compiler\HandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CommandSupervisorBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new HandlerPass());
    }
}
