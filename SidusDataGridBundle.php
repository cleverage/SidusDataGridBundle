<?php

namespace Sidus\DataGridBundle;

use Sidus\BaseBundle\DependencyInjection\Compiler\GenericCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SidusDataGridBundle
 *
 * @package Sidus\DataGridBundle
 */
class SidusDataGridBundle extends Bundle
{
    /**
     * Adding compiler passes to inject services into configuration handlers
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new GenericCompilerPass(
            'sidus_data_grid.registry.datagrid',
            'sidus.datagrid',
            'addDataGrid'
        ));
    }
}
