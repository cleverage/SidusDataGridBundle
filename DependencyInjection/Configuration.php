<?php

namespace Sidus\DataGridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    protected $root;

    /**
     * @param string $root
     */
    public function __construct($root = 'sidus_data_grid')
    {
        $this->root = $root;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->root);
        $rootNode
            ->children()
                ->scalarNode('default_form_theme')->defaultValue('SidusDataGridBundle:Form:filter_theme.html.twig')->end()
                ->scalarNode('default_renderer')->defaultValue(new Reference('sidus_data_grid.renderer.twig'))->end()
                ->arrayNode('configurations')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('filter_config')->isRequired(true)->end()
                            ->scalarNode('form_theme')->defaultNull()->end()
                            ->scalarNode('renderer')->defaultNull()->end()
                            ->variableNode('actions')->end()
                            ->arrayNode('columns')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('template')->defaultNull()->end()
                                        ->scalarNode('sort_column')->defaultNull()->end()
                                        ->scalarNode('property_path')->defaultNull()->end()
                                        ->scalarNode('label')->defaultNull()->end()
                                        ->scalarNode('renderer')->defaultNull()->end()
                                        ->variableNode('formatting_options')->defaultValue([])->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
