<?php

namespace WS\DeploymentAssistant;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class ApplicationConfiguration
 * @package WS\DeployAssistant
 */
class Configuration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('app');
        $rootNode
            ->children()
                ->arrayNode('update')
                    ->children()
                        ->scalarNode('phar_url')->end()
                        ->scalarNode('sum_url')->end()
                    ->end()
                ->end()
                ->arrayNode('sentry')
                    ->children()
                        ->scalarNode('dsn')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
