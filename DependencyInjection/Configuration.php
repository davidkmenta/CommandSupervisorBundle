<?php

namespace DavidKmenta\CommandSupervisorBundle\DependencyInjection;

use DavidKmenta\CommandSupervisorBundle\DependencyInjection\Compiler\HandlerPass;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const DEFAULT_HANDLER_SERVICE_ID = 'command_supervisor.handler.swift_mailer_handler';

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('command_supervisor');
        $rootNode
            ->children()
                ->arrayNode('commands')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('name')
                                ->cannotBeEmpty()
                            ->end()
                            ->integerNode('threshold')
                                ->min(1)
                            ->end()
                            ->scalarNode('handler')
                                ->defaultValue(HandlerPass::DEFAULT_HANDLER_NAME)
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('cache_path')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('default_handler')
                    ->defaultValue(self::DEFAULT_HANDLER_SERVICE_ID)
                ->end()
                ->arrayNode('handlers')
                    ->useAttributeAsKey('name')
                    ->prototype('scalar')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
