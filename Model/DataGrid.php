<?php

namespace Sidus\DataGridBundle\Model;

use Sidus\DataGridBundle\Templating\Renderable;
use Sidus\FilterBundle\Configuration\FilterConfigurationHandler;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    /** @var Form */
    protected $form;

    /** @var FormView */
    protected $formView;

    /** @var array */
    protected $actions;

    /** @var array */
    protected $submitButton = [];

    /** @var array */
    protected $resetButton = [];

    /**
     * DataGrid constructor.
     * @param string $code
     * @param array $configuration
     * @throws \Exception
     */
    public function __construct($code, array $configuration)
    {
        $this->code = $code;
        $columns = $configuration['columns'];
        unset($configuration['columns']);

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($configuration as $key => $option) {
            $accessor->setValue($this, $key, $option);
        }

        foreach ($columns as $key => $columnConfiguration) {
            $this->createColumn($key, $columnConfiguration);
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
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param string $action
     * @return array
     * @throws \UnexpectedValueException
     */
    public function getAction($action)
    {
        if (!$this->hasAction($action)) {
            throw new \UnexpectedValueException("No action with code: '{$action}'");
        }
        return $this->actions[$action];
    }

    /**
     * @param string $action
     * @return bool
     */
    public function hasAction($action)
    {
        return array_key_exists($action, $this->actions);
    }

    /**
     * @param string $action
     * @param array $configuration
     * @return DataGrid
     */
    public function setAction($action, array $configuration)
    {
        $this->actions[$action] = $configuration;
        return $this;
    }

    /**
     * @param array $actions
     * @return DataGrid
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * @return array
     */
    public function getSubmitButton()
    {
        return $this->submitButton;
    }

    /**
     * @param array $submitButton
     * @return DataGrid
     */
    public function setSubmitButton(array $submitButton)
    {
        $this->submitButton = $submitButton;
        return $this;
    }

    /**
     * @return array
     */
    public function getResetButton()
    {
        return $this->resetButton;
    }

    /**
     * @param array $resetButton
     * @return DataGrid
     */
    public function setResetButton(array $resetButton)
    {
        $this->resetButton = $resetButton;
        return $this;
    }

    /**
     * @return Form
     * @throws \LogicException
     */
    public function getForm()
    {
        if (!$this->form) {
            throw new \LogicException('You must first call buildForm()');
        }
        return $this->form;
    }

    public function getFormView()
    {
        if (!$this->formView) {
            $this->formView = $this->getForm()->createView();
        }
        return $this->formView;
    }

    /**
     * @param FormBuilder $builder
     * @return $this
     * @throws \Exception
     */
    public function buildForm(FormBuilder $builder)
    {
        $this->buildFilterActions($builder);
        $this->buildDataGridActions($builder);

        $this->form = $this->getFilterConfig()->buildForm($builder);
        return $this->form;
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function handleRequest(Request $request)
    {
        $this->filterConfig->handleRequest($request);
    }

    /**
     * @param FormBuilder $builder
     * @throws \Exception
     */
    protected function buildFilterActions(FormBuilder $builder)
    {
        if (count($this->getFilterConfig()->getFilters()) > 0) {
            $this->buildResetAction($builder);
            $this->buildSubmitAction($builder);
        }
    }

    /**
     * @param FormBuilder $builder
     * @throws \Exception
     */
    protected function buildResetAction(FormBuilder $builder)
    {
        $action = $builder->getOption('action');
        $defaults = [
            'form_type' => 'sidus_link',
            'label' => 'sidus.datagrid.reset.label',
            'uri' => $action ?: '?',
            'icon' => 'close',
        ];
        $options = array_merge($defaults, $this->getResetButton());
        $type = $options['form_type'];
        unset($options['form_type']);
        $builder->add('filterResetButton', $type, $options);
    }

    /**
     * @param FormBuilder $builder
     * @throws \Exception
     */
    protected function buildSubmitAction(FormBuilder $builder)
    {
        $defaults = [
            'form_type' => 'submit',
            'label' => 'sidus.datagrid.submit.label',
            'icon' => 'filter',
            'attr' => [
                'class' => 'btn-primary',
            ],
        ];
        $options = array_merge($defaults, $this->getSubmitButton());
        $type = $options['form_type'];
        unset($options['form_type']);
        $builder->add('filterSubmitButton', $type, $options);
    }

    /**
     * @param FormBuilder $builder
     */
    protected function buildDataGridActions(FormBuilder $builder)
    {
        $actionsBuilder = $builder->create('actions', 'form', [
            'label' => false,
        ]);
        foreach ($this->getActions() as $code => $options) {
            $type = empty($options['form_type']) ? 'sidus_link' : $options['form_type'];
            unset($options['form_type']);
            $actionsBuilder->add($code, $type, $options);
        }
        $builder->add($actionsBuilder);
    }

    /**
     * @param string $action
     * @param array $parameters
     */
    public function setActionParameters($action, array $parameters)
    {
        if ($action === 'submit_button') {
            $this->setSubmitButton(array_merge($this->getSubmitButton(), [
                'route_parameters' => $parameters,
            ]));
            return;
        }
        if ($action === 'reset_button') {
            $this->setResetButton(array_merge($this->getResetButton(), [
                'route_parameters' => $parameters,
            ]));
            return;
        }
        $this->setAction($action, array_merge($this->getAction($action), [
            'route_parameters' => $parameters,
        ]));
    }

    /**
     * @param string $key
     * @param array $columnConfiguration
     * @throws \Exception
     */
    protected function createColumn($key, array $columnConfiguration)
    {
        $this->columns[] = new Column($key, $this, $columnConfiguration);
    }
}
