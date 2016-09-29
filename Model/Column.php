<?php

namespace Sidus\DataGridBundle\Model;

use Sidus\DataGridBundle\Templating\Renderable;
use Symfony\Component\PropertyAccess\PropertyAccess;

class Column implements Renderable
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

    /** @var Renderable */
    protected $renderer;

    /** @var array */
    protected $formattingOptions;

    /** @var string */
    protected $label;

    /**
     * Column constructor.
     *
     * @param string   $code
     * @param DataGrid $dataGrid
     * @param array    $options
     *
     * @throws \Exception
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return Column
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return DataGrid
     */
    public function getDataGrid()
    {
        return $this->dataGrid;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param string $template
     *
     * @return Column
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return string
     */
    public function getSortColumn()
    {
        if (!$this->sortColumn) {
            return $this->getCode();
        }

        return $this->sortColumn;
    }

    /**
     * @param string $sortColumn
     *
     * @return Column
     */
    public function setSortColumn($sortColumn)
    {
        $this->sortColumn = $sortColumn;

        return $this;
    }

    /**
     * @return string
     */
    public function getPropertyPath()
    {
        if (!$this->propertyPath) {
            return $this->getCode();
        }

        return $this->propertyPath;
    }

    /**
     * @param string $propertyPath
     *
     * @return Column
     */
    public function setPropertyPath($propertyPath)
    {
        $this->propertyPath = $propertyPath;

        return $this;
    }

    /**
     * @return Renderable
     */
    public function getRenderer()
    {
        if (!$this->renderer) {
            return $this->getDataGrid()->getRenderer();
        }

        return $this->renderer;
    }

    /**
     * @param Renderable $renderer
     *
     * @return Column
     */
    public function setRenderer(Renderable $renderer = null)
    {
        $this->renderer = $renderer;

        return $this;
    }

    /**
     * @return array
     */
    public function getFormattingOptions()
    {
        return $this->formattingOptions;
    }

    /**
     * @param array $formattingOptions
     *
     * @return Column
     */
    public function setFormattingOptions(array $formattingOptions)
    {
        $this->formattingOptions = $formattingOptions;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        if (!$this->label) {
            return ucfirst(
                trim(strtolower(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $this->getCode())))
            );
        }

        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return Column
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @param mixed $value
     * @param array $options
     *
     * @return string
     */
    public function renderValue($value, array $options = [])
    {
        return $this->getRenderer()->renderValue($value, array_merge($this->getFormattingOptions(), $options));
    }
}
