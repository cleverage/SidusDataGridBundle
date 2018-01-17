<?php

namespace Sidus\DataGridBundle\Twig;

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

    /**
     * @param RenderableInterface $renderer
     */
    public function __construct(RenderableInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('render_value', [$this->renderer, 'renderValue'], ['is_safe' => ['html']]),
        ];
    }
}
