<?php

namespace Sidus\DataGridBundle\Twig;

use Sidus\DataGridBundle\Model\DataGrid;
use Sidus\DataGridBundle\Renderer\ColumnRendererInterface;
use Sidus\DataGridBundle\Renderer\RenderableInterface;
use Twig_Environment;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Render values inside the Twig engine
 */
class RendererExtension extends Twig_Extension
{
    /** @var Twig_Environment */
    protected $twig;

    /** @var RenderableInterface */
    protected $renderer;

    /** @var ColumnRendererInterface */
    protected $columnRenderer;

    /**
     * @param Twig_Environment        $twig
     * @param RenderableInterface     $renderer
     * @param ColumnRendererInterface $columnRenderer
     */
    public function __construct(
        Twig_Environment $twig,
        RenderableInterface $renderer,
        ColumnRendererInterface $columnRenderer
    ) {
        $this->twig = $twig;
        $this->renderer = $renderer;
        $this->columnRenderer = $columnRenderer;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction(
                'render_datagrid',
                [$this, 'renderDataGrid'],
                ['is_safe' => ['html']]
            ),
            new Twig_SimpleFunction(
                'render_value',
                [$this->renderer, 'renderValue'],
                ['is_safe' => ['html']]
            ),
            new Twig_SimpleFunction(
                'render_column_label',
                [$this->columnRenderer, 'renderColumnLabel'],
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
        return $this->twig->render(
            $dataGrid->getTemplate(),
            ['datagrid' => $dataGrid, 'viewParameters' => $viewParameters]
        );
    }
}
