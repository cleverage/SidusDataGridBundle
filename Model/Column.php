<?php
/*
 * This file is part of the Sidus/DataGridBundle package.
 *
 * Copyright (c) 2015-2018 Vincent Chalnot
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sidus\DataGridBundle\Model;

use Sidus\DataGridBundle\Renderer\ColumnLabelRendererInterface;
use Sidus\DataGridBundle\Renderer\ColumnValueRendererInterface;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Represents a column configuration for a datagrid
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class Column
{
    /** @var string */
    protected $code;

    /** @var DataGrid */
    protected $dataGrid;

    /** @var string */
    protected $template;

    /** @var array */
    protected $templateVars = [];

    /** @var string */
    protected $sortColumn;

    /** @var string */
    protected $propertyPath;

    /** @var ColumnValueRendererInterface */
    protected $valueRenderer;

    /** @var ColumnLabelRendererInterface */
    protected $labelRenderer;

    /** @var array */
    protected $formattingOptions = [];

    /** @var string */
    protected $label;

    /**
     * @param string   $code
     * @param DataGrid $dataGrid
     * @param array    $options
     */
    public function __construct($code, DataGrid $dataGrid, array $options = [])
    {
        $this->code = $code;
        $this->dataGrid = $dataGrid;
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($options as $key => $option) {
            $accessor->setValue($this, $key, $option);
        }
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @return DataGrid
     */
    public function getDataGrid(): DataGrid
    {
        return $this->dataGrid;
    }

    /**
     * @return string|null
     */
    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    /**
     * @return array
     */
    public function getTemplateVars(): array
    {
        return $this->templateVars;
    }

    /**
     * @param array $templateVars
     */
    public function setTemplateVars(array $templateVars): void
    {
        $this->templateVars = $templateVars;
    }

    /**
     * @return string
     */
    public function getSortColumn(): string
    {
        if (!$this->sortColumn) {
            return $this->getCode();
        }

        return $this->sortColumn;
    }

    /**
     * @param string $sortColumn
     */
    public function setSortColumn(string $sortColumn): void
    {
        $this->sortColumn = $sortColumn;
    }

    /**
     * @return string
     */
    public function getPropertyPath(): string
    {
        if (!$this->propertyPath) {
            return $this->getCode();
        }

        return $this->propertyPath;
    }

    /**
     * @param string $propertyPath
     */
    public function setPropertyPath(string $propertyPath): void
    {
        $this->propertyPath = $propertyPath;
    }

    /**
     * @return ColumnValueRendererInterface
     */
    public function getValueRenderer(): ColumnValueRendererInterface
    {
        if (!$this->valueRenderer) {
            return $this->getDataGrid()->getColumnValueRenderer();
        }

        return $this->valueRenderer;
    }

    /**
     * @param ColumnValueRendererInterface $valueRenderer
     */
    public function setValueRenderer(ColumnValueRendererInterface $valueRenderer): void
    {
        $this->valueRenderer = $valueRenderer;
    }

    /**
     * @return ColumnLabelRendererInterface
     */
    public function getLabelRenderer(): ColumnLabelRendererInterface
    {
        if (null === $this->labelRenderer) {
            return $this->getDataGrid()->getColumnLabelRenderer();
        }

        return $this->labelRenderer;
    }

    /**
     * @param ColumnLabelRendererInterface $labelRenderer
     */
    public function setLabelRenderer(ColumnLabelRendererInterface $labelRenderer): void
    {
        $this->labelRenderer = $labelRenderer;
    }

    /**
     * @return array
     */
    public function getFormattingOptions(): array
    {
        return $this->formattingOptions;
    }

    /**
     * @param array $formattingOptions
     */
    public function setFormattingOptions(array $formattingOptions): void
    {
        $this->formattingOptions = $formattingOptions;
    }

    /**
     * @return string
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    /**
     * Render column for a given result
     *
     * @param mixed $object
     * @param array $options
     *
     * @return string
     */
    public function renderValue($object, array $options = []): string
    {
        $options = array_merge(
            ['column' => $this, 'object' => $object],
            $this->getFormattingOptions(),
            $options
        );
        $accessor = PropertyAccess::createPropertyAccessor();
        try {
            $value = $accessor->getValue($object, $this->getPropertyPath());
        } catch (UnexpectedTypeException $e) {
            return '';
        }

        return $this->getValueRenderer()->renderValue($value, $options);
    }

    /**
     * @return string
     */
    public function renderLabel(): string
    {
        return ucfirst($this->getLabelRenderer()->renderColumnLabel($this));
    }
}
