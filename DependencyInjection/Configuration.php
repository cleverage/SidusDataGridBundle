<?php

namespace Sidus\DataGridBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link
 * http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
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
     * @throws \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->root);
        $rootNode
            ->children()
            ->scalarNode('default_form_theme')->defaultValue('SidusDataGridBundle:Form:filter_theme.html.twig')->end()
            ->scalarNode('default_renderer')->defaultValue(new Reference('sidus_data_grid.renderer.twig'))->end()
            ->append($this->getDataGridConfigTreeBuilder())
            ->variableNode('actions')->defaultValue([])->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @return NodeDefinition
     * @throws \RuntimeException
     */
    protected function getDataGridConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $node = $builder->root('configurations');
        $dataGridDefinition = $node
            ->useAttributeAsKey('code')
            ->prototype('array')
            ->performNoDeepMerging()
            ->children();

        $this->appendDataGridDefinition($dataGridDefinition);

        $dataGridDefinition->end()
            ->end()
            ->end();

        return $node;
    }

    /**
     * @param NodeBuilder $dataGridDefinition
     */
    protected function appendDataGridDefinition(NodeBuilder $dataGridDefinition)
    {
        $dataGridDefinition
            ->variableNode('filter_config')->end()
            ->scalarNode('form_theme')->defaultNull()->end()
            ->scalarNode('parent')->defaultNull()->end()
            ->scalarNode('renderer')->defaultNull()->end()
            ->variableNode('actions')->end()
            ->variableNode('submit_button')->defaultValue([])->end()
            ->variableNode('reset_button')->defaultValue([])->end()
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
            ->end();
    }
}
