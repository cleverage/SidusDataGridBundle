<?php

namespace Sidus\DataGridBundle\DependencyInjection;

use Sidus\FilterBundle\DependencyInjection\Configuration as FilterConfiguration;
use Sidus\FilterBundle\DependencyInjection\Loader\ServiceLoader;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
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
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new ServiceLoader(__DIR__.'/../Resources/config/services');
        $loader->loadFiles($container);

        $configuration = $this->createConfigurationParser();
        $this->globalConfiguration = $this->processConfiguration($configuration, $configs);

        $dataGridRegistry = $container->getDefinition('sidus_data_grid.registry.datagrid');
        foreach ((array) $this->globalConfiguration['configurations'] as $code => $dataGridConfiguration) {
            $dataGridConfiguration = $this->finalizeConfiguration($code, $dataGridConfiguration);
            $dataGridRegistry->addMethodCall('addRawDataGridConfiguration', [$code, $dataGridConfiguration]);
        }
    }

    /**
     * Handle configuration parsing logic not handled by the semantic configuration definition
     *
     * @param string $code
     * @param array  $dataGridConfiguration
     *
     * @throws \Exception
     *
     * @return array
     */
    protected function finalizeConfiguration(
        string $code,
        array $dataGridConfiguration
    ): array {
        // Handle possible parent configuration @todo find a better way to do this
        if (isset($dataGridConfiguration['parent'])) {
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

        if (isset($dataGridConfiguration['query_handler'])) {
            // Allow either a service or a direct configuration for filters
            if (is_array($dataGridConfiguration['query_handler'])) {
                $dataGridConfiguration['query_handler'] = $this->finalizeFilterConfiguration(
                    $code,
                    $dataGridConfiguration['query_handler']
                );
            } elseif (0 === strpos($dataGridConfiguration['query_handler'], '@')) {
                $dataGridConfiguration['query_handler'] = new Reference(
                    ltrim($dataGridConfiguration['query_handler'], '@')
                );
            } else {
                throw new UnexpectedValueException(
                    'query_handler option must be either a service or a valid filter configuration'
                );
            }
        }

        return $dataGridConfiguration;
    }

    /**
     * @param string $code
     * @param array  $queryHandlerConfig
     *
     * @return array
     */
    protected function finalizeFilterConfiguration(string $code, array $queryHandlerConfig): array
    {
        // Parse configuration using Configuration parser from FilterBundle
        $configuration = $this->createFilterConfigurationParser();
        $parsedFilterConfig = $this->processConfiguration(
            $configuration,
            [
                [
                    'configurations' => [
                        $code => $queryHandlerConfig,
                    ],
                ],
            ]
        );

        return $parsedFilterConfig['configurations'][$code];
    }

    /**
     * Allows the configuration class to be different in inherited classes
     *
     * @return ConfigurationInterface
     */
    protected function createConfigurationParser(): ConfigurationInterface
    {
        return new Configuration();
    }

    /**
     * Allows the configuration class to be different in inherited classes
     *
     * @return ConfigurationInterface
     */
    protected function createFilterConfigurationParser(): ConfigurationInterface
    {
        return new FilterConfiguration();
    }
}
