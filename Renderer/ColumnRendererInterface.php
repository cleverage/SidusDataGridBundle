<?php

namespace Sidus\DataGridBundle\Renderer;

use Sidus\DataGridBundle\Model\Column;

/**
 * Allows an object to be rendered in a templating engine
 */
interface ColumnRendererInterface
{
    /**
     * @param Column $column
     *
     * @return string
     */
    public function renderColumnLabel(Column $column): string;
}
