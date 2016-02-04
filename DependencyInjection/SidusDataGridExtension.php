<?php

namespace Sidus\DataGridBundle\DependencyInjection;

use ReflectionClass;
use Sidus\FilterBundle\DependencyInjection\Configuration as FilterConfiguration;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use UnexpectedValueException;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SidusDataGridExtension extends Extension
{
    /** @var array */
    protected $globalConfiguration;

    /**
     * {@inheritdoc}
     * @throws \Exception
     * @throws BadMethodCallException
     * @throws UnexpectedValueException
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->createConfigurationParser();
        $this->globalConfiguration = $this->processConfiguration($configuration, $configs);

        foreach ($this->globalConfiguration['configurations'] as $code => $dataGridConfiguration) {
            $this->addDataGridServiceDefinition($code, $dataGridConfiguration, $container);
        }

        $classInfo = new ReflectionClass($this);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(dirname($classInfo->getFileName()).'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Handle configuration parsing logic not handled by the semantic configuration definition
     *
     * @param $code
     * @param $dataGridConfiguration
     * @param ContainerBuilder $container
     * @return array
     * @throws BadMethodCallException
     * @throws UnexpectedValueException
     */
    protected function finalizeConfiguration($code, array $dataGridConfiguration, ContainerBuilder $container)
    {
        // Handle possible parent configuration
        if ($dataGridConfiguration['parent']) {
            $parent = $dataGridConfiguration['parent'];
            if (empty($this->globalConfiguration['configurations'][$parent])) {
                throw new UnexpectedValueException("Unknown configuration {$parent}");
            }
            $parentConfig = $this->globalConfiguration['configurations'][$parent];
            $dataGridConfiguration = array_merge($parentConfig, $dataGridConfiguration);
        }
        unset($dataGridConfiguration['parent']);

        // Set default values from global configuration
        if (empty($dataGridConfiguration['form_theme'])) {
            $dataGridConfiguration['form_theme'] = $this->globalConfiguration['default_form_theme'];
        }
        if (empty($dataGridConfiguration['renderer'])) {
            $dataGridConfiguration['renderer'] = $this->globalConfiguration['default_renderer'];
        }

        // Allow either a service or a direct configuration for filters
        if (is_array($dataGridConfiguration['filter_config'])) {
            $dataGridConfiguration['filter_config'] = $this->addFilterConfiguration($code, $dataGridConfiguration, $container);
        } elseif (substr($dataGridConfiguration['filter_config'], 0, 1) === '@') {
            $dataGridConfiguration['filter_config'] = new Reference(ltrim($dataGridConfiguration['filter_config'], '@'));
        } else {
            throw new UnexpectedValueException('filter_config option must be either a service or a valid filter configuration');
        }

        return $dataGridConfiguration;
    }

    /**
     * Add a new Filter service based on the configuration passed inside the datagrid
     *
     * @param string $code
     * @param array $dataGridConfiguration
     * @param ContainerBuilder $container
     * @throws BadMethodCallException
     * @throws UnexpectedValueException
     */
    protected function addDataGridServiceDefinition($code, array $dataGridConfiguration, ContainerBuilder $container)
    {
        $dataGridConfiguration = $this->finalizeConfiguration($code, $dataGridConfiguration, $container);

        $definition = new Definition(new Parameter('sidus_data_grid.model.datagrid.class'), [
            $code,
            $dataGridConfiguration,
        ]);
        $definition->addTag('sidus.datagrid');
        $container->setDefinition('sidus_data_grid.datagrid.' . $code, $definition);
    }

    /**
     * @param $code
     * @param array $filterConfig
     * @return array
     */
    protected function finalizeFilterConfiguration($code, array $filterConfig)
    {
        // Parse configuration using Configuration parser from FilterBundle
        $configuration = $this->createFilterConfigurationParser();
        $parsedFilterConfig = $this->processConfiguration($configuration, [
            [
                'configurations' => [
                    $code => $filterConfig,
                ],
            ],
        ]);
        return $parsedFilterConfig['configurations'][$code];
    }

    /**
     * Handle direct configuration of filters, uses the same logic than the FilterBundle to generate a service
     *
     * @param $code
     * @param array $dataGridConfiguration
     * @param ContainerBuilder $container
     * @return Reference
     * @throws BadMethodCallException
     */
    protected function addFilterConfiguration($code, array $dataGridConfiguration, ContainerBuilder $container)
    {
        $filterConfig = $this->finalizeFilterConfiguration($code, $dataGridConfiguration['filter_config']);

        $definition = new Definition(new Parameter('sidus_filter.configuration.class'), [
            $code,
            new Reference('doctrine'),
            new Reference('sidus_filter.filter.factory'),
            $filterConfig,
        ]);

        $serviceId = 'sidus_filter.datagrid.configuration.' . $code;
        $container->setDefinition($serviceId, $definition);
        return new Reference($serviceId);
    }

    /**
     * Allows the configuration class to be different in inherited classes
     * @return Configuration
     */
    protected function createConfigurationParser()
    {
        return new Configuration();
    }

    /**
     * Allows the configuration class to be different in inherited classes
     * @return FilterConfiguration
     */
    protected function createFilterConfigurationParser()
    {
        return new FilterConfiguration();
    }
}
