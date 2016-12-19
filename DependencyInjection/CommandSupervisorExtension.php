<?php

namespace DavidKmenta\CommandSupervisorBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class CommandSupervisorExtension extends ConfigurableExtension
{
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->setParameter('command_supervisor.commands', $mergedConfig['commands']);
        $container->setParameter('command_supervisor.cache_path', $mergedConfig['cache_path']);
        $container->setParameter('command_supervisor.default_handler', $mergedConfig['default_handler']);
        $container->setParameter('command_supervisor.handlers', $mergedConfig['handlers']);

        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loaderResolver = new LoaderResolver(
            [
                new Loader\YamlFileLoader($container, $locator),
                new Loader\XmlFileLoader($container, $locator),
            ]
        );

        $delegatingLoader = new DelegatingLoader($loaderResolver);
        $delegatingLoader->load('services.xml');
    }
}
