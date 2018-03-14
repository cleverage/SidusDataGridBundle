<?php

namespace Sidus\DataGridBundle\Renderer;

/**
 * Allows an object to be rendered in a templating engine
 */
interface RenderableInterface
{
    /**
     * @param mixed $value
     * @param array $options
     *
     * @return string
     */
    public function renderValue($value, array $options = []): string;
}
