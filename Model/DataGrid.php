<?php

namespace Sidus\DataGridBundle\Model;

use Sidus\DataGridBundle\Templating\Renderable;
use Sidus\FilterBundle\Configuration\FilterConfigurationHandler;
use Symfony\Component\Form\FormView;

class DataGrid
{
    /** @var string */
    protected $code;

    /** @var FilterConfigurationHandler */
    protected $filterConfig;

    /** @var string */
    protected $formTheme;

    /** @var Renderable */
    protected $renderer;

    /** @var Column[] */
    protected $columns = [];

    /** @var FormView */
    protected $form;

    /**
     * DataGrid constructor.
     * @param string $code
     * @param FilterConfigurationHandler $filterConfig
     * @param Renderable $renderer
     * @param array $configuration
     * @throws \Exception
     */
    public function __construct($code, FilterConfigurationHandler $filterConfig, Renderable $renderer, array $configuration)
    {
        $this->code = $code;
        $this->filterConfig = $filterConfig;
        $this->renderer = $renderer;
        foreach ($configuration['columns'] as $key => $columnConfiguration) {
            $this->columns[] = new Column($key, $this, $columnConfiguration);
        }
        $this->formTheme = $configuration['form_theme'];
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
     * @return DataGrid
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return FilterConfigurationHandler
     */
    public function getFilterConfig()
    {
        return $this->filterConfig;
    }

    /**
     * @param FilterConfigurationHandler $filterConfig
     * @return DataGrid
     */
    public function setFilterConfig(FilterConfigurationHandler $filterConfig)
    {
        $this->filterConfig = $filterConfig;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormTheme()
    {
        return $this->formTheme;
    }

    /**
     * @param string $formTheme
     * @return DataGrid
     */
    public function setFormTheme($formTheme)
    {
        $this->formTheme = $formTheme;
        return $this;
    }

    /**
     * @return Renderable
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param Renderable $renderer
     * @return DataGrid
     */
    public function setRenderer(Renderable $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param Column $column
     * @return DataGrid
     */
    public function addColumn(Column $column)
    {
        $this->columns[] = $column;
        return $this;
    }

    /**
     * @param Column[] $columns
     * @return DataGrid
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @return FormView
     * @throws \LogicException
     */
    public function getForm()
    {
        if (!$this->form) {
            $this->form = $this->getFilterConfig()->getForm()->createView();
        }
        return $this->form;
    }
}