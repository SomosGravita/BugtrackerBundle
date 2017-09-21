<?php

namespace Elemento115\BugtrackerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

use Elemento115\BugtrackerBundle\Classes\Constants;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('bugtracker');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.
        $rootNode
            ->children()
                ->scalarNode(Constants::API_USER)->end()
                ->scalarNode(Constants::API_PASSWORD)->end()
                ->scalarNode(Constants::API_URL)->end()
            ->end()
            ->children()
                    ->arrayNode('registries')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('environment')->end()
                            ->scalarNode('token')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
