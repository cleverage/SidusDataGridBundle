<?php

namespace Sidus\DataGridBundle;

use Sidus\DataGridBundle\DependencyInjection\Compiler\DataGridCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SidusDataGridBundle extends Bundle
{
    /**
     * Adding compiler passes to inject services into configuration handlers
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new DataGridCompilerPass());
    }
}
