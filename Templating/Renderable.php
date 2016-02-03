<?php

namespace Sidus\DataGridBundle\Templating;


interface Renderable
{
    /**
     * @param mixed $value
     * @param array $options
     * @return string
     */
    public function renderValue($value, array $options = []);
}