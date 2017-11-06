<?php

namespace Sidus\DataGridBundle\Model;

use Sidus\DataGridBundle\Templating\RenderableInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Represents a column configuration for a datagrid
 */
class Column implements RenderableInterface
{
    /** @var string */
    protected $code;

    /** @var DataGrid */
    protected $dataGrid;

    /** @var string */
    protected $template;

    /** @var string */
    protected $sortColumn;

    /** @var string */
    protected $propertyPath;

    /** @var RenderableInterface */
    protected $renderer;

    /** @var array */
    protected $formattingOptions = [];

    /** @var string */
    protected $label;

    /**
     * Column constructor.
     *
     * @param string   $code
     * @param DataGrid $dataGrid
     * @param array    $options
     *
     * @throws \Symfony\Component\PropertyAccess\Exception\ExceptionInterface
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
    public function setCode(string $code)
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
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate(string $template)
    {
        $this->template = $template;
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
    public function setSortColumn(string $sortColumn)
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
    public function setPropertyPath(string $propertyPath)
    {
        $this->propertyPath = $propertyPath;
    }

    /**
     * @return RenderableInterface
     */
    public function getRenderer(): RenderableInterface
    {
        if (!$this->renderer) {
            return $this->getDataGrid()->getRenderer();
        }

        return $this->renderer;
    }

    /**
     * @param RenderableInterface $renderer
     */
    public function setRenderer(RenderableInterface $renderer)
    {
        $this->renderer = $renderer;
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
    public function setFormattingOptions(array $formattingOptions)
    {
        $this->formattingOptions = $formattingOptions;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        if (!$this->label) {
            return ucfirst(
                strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $this->getCode())))
            );
        }

        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @param mixed $value
     * @param array $options
     *
     * @return string
     */
    public function renderValue($value, array $options = []): string
    {
        return (string) $this->getRenderer()->renderValue($value, array_merge($this->getFormattingOptions(), $options));
    }
}
