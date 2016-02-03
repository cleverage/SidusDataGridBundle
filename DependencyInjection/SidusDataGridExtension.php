<?php

namespace Sidus\DataGridBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SidusDataGridExtension extends Extension
{
    /**
     * {@inheritdoc}
     * @throws \Exception
     * @throws BadMethodCallException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        foreach ($config['configurations'] as $code => $configuration) {
            if (empty($configuration['form_theme'])) {
                $configuration['form_theme'] = $config['default_form_theme'];
            }
            if (empty($configuration['renderer'])) {
                $configuration['renderer'] = $config['default_renderer'];
            }
            $this->addDataGridServiceDefinition($code, $configuration, $container);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * @param string $code
     * @param array $dataGridConfiguration
     * @param ContainerBuilder $container
     * @throws BadMethodCallException
     */
    protected function addDataGridServiceDefinition($code, $dataGridConfiguration, ContainerBuilder $container)
    {
        $filterConfig = new Reference(ltrim($dataGridConfiguration['filter_config'], '@'));
        unset($dataGridConfiguration['filter_config']);

        $renderer = $dataGridConfiguration['renderer'];
        unset($dataGridConfiguration['renderer']);

        $definition = new Definition(new Parameter('sidus_data_grid.model.datagrid.class'), [
            $code,
            $filterConfig,
            $renderer,
            $dataGridConfiguration,
        ]);
        $definition->addTag('sidus.datagrid');
        $container->setDefinition('sidus_data_grid.datagrid.' . $code, $definition);
    }
}
