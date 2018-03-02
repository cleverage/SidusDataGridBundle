<?php

namespace Sidus\DataGridBundle\Twig;

use Sidus\DataGridBundle\Renderer\ColumnRendererInterface;
use Sidus\DataGridBundle\Renderer\RenderableInterface;
use Twig_Extension;
use Twig_SimpleFunction;

/**
 * Render values inside the Twig engine
 */
class RendererExtension extends Twig_Extension
{
    /** @var RenderableInterface */
    protected $renderer;

    /** @var ColumnRendererInterface */
    protected $columnRenderer;

    /**
     * @param RenderableInterface     $renderer
     * @param ColumnRendererInterface $columnRenderer
     */
    public function __construct(RenderableInterface $renderer, ColumnRendererInterface $columnRenderer)
    {
        $this->renderer = $renderer;
        $this->columnRenderer = $columnRenderer;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('render_value', [$this->renderer, 'renderValue'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('render_column_label', [$this->columnRenderer, 'renderColumnLabel'], ['is_safe' => ['html']]),
        ];
    }
}
