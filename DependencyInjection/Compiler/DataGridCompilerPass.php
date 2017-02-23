<?php

namespace Sidus\DataGridBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Reference;

class DataGridCompilerPass implements CompilerPassInterface
{
    /**
     * Inject tagged datagrids into configuration handler
     *
     * @param ContainerBuilder $container
     *
     * @api
     * @throws InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('sidus_data_grid.datagrid_configuration.handler')) {
            return;
        }

        $definition = $container->findDefinition('sidus_data_grid.datagrid_configuration.handler');
        $taggedServices = $container->findTaggedServiceIds('sidus.datagrid');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall(
                'addDataGrid',
                [new Reference($id)]
            );
        }
    }
}
