<?php
/*
 * This file is part of the Sidus/DataGridBundle package.
 *
 * Copyright (c) 2015-2018 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\DataGridBundle\Renderer;

/**
 * Allows an object to be rendered in a templating engine
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
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
