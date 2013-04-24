<?php

namespace Heystack\Subsystem\Payment\DPS\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * pxfusion:
 *   config:
 *     Type: Auth-Complete
 *     Username: HeydayPXFDev
 *     Password: test1234
 * pxpost:
 *   config:
 *     Username: HeydayDev
 *     Password: post1234
 * Class ContainerConfig
 * @package Heystack\Subsystem\Payment\DPS\Config
 */
class ContainerConfig implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dps');

        $rootNode
            ->children()
                ->arrayNode('pxfusion')
                    ->children()
                        ->booleanNode('testing')->end()
                        ->arrayNode('config')
                            ->children()
                                ->scalarNode('Type')->end()
                                ->scalarNode('Username')->end()
                                ->scalarNode('Password')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pxpost')
                    ->children()
                        ->booleanNode('testing')->end()
                        ->arrayNode('config')
                            ->children()
                                ->scalarNode('Type')->end()
                                ->scalarNode('Username')->end()
                                ->scalarNode('Password')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}