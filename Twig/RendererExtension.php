<?php
/*
 * This file is part of the Sidus/DataGridBundle package.
 *
 * Copyright (c) 2015-2018 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\DataGridBundle\Twig;

use Sidus\DataGridBundle\Model\DataGrid;
use Symfony\Component\Form\FormView;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Render values inside the Twig engine
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class RendererExtension extends Twig_Extension
{
    /** @var Twig_Environment */
    protected $twig;

    /**
     * @param Twig_Environment $twig
     */
    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction(
                'render_datagrid',
                [$this, 'renderDataGrid'],
                ['is_safe' => ['html']]
            ),
            new Twig_SimpleFunction(
                'get_filter_columns',
                [$this, 'getFilterColumns'],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param DataGrid $dataGrid
     * @param array    $viewParameters
     *
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     *
     * @return string
     */
    public function renderDataGrid(DataGrid $dataGrid, array $viewParameters = []): string
    {
        $viewParameters = array_merge($dataGrid->getTemplateVars(), $viewParameters);
        $viewParameters['datagrid'] = $dataGrid;

        return $this->twig->render($dataGrid->getTemplate(), $viewParameters);
    }

    /**
     * Simple function to split form widgets in as many columns as wanted
     *
     * @param FormView $formView
     * @param int      $numColumns
     *
     * @return array
     */
    public function getFilterColumns(FormView $formView, int $numColumns = 3): array
    {
        $columns = [];
        $i = 0;
        foreach ($formView as $formItem) {
            $columns[$i % $numColumns][] = $formItem;
            ++$i;
        }

        return $columns;
    }
}
