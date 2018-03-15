<?php

namespace Sidus\DataGridBundle\Model;

use Pagerfanta\Exception\InvalidArgumentException;
use Sidus\DataGridBundle\Form\Type\LinkType;
use Sidus\DataGridBundle\Renderer\RenderableInterface;
use Sidus\FilterBundle\Query\Handler\QueryHandlerInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Handle a datagrid configuration
 *
 * @author Vincent Chalnot <vincent@sidus.fr>
 */
class DataGrid
{
    /** @var string */
    protected $code;

    /** @var QueryHandlerInterface */
    protected $queryHandler;

    /** @var string */
    protected $formTheme;

    /** @var string */
    protected $template;

    /** @var RenderableInterface */
    protected $renderer;

    /** @var Column[] */
    protected $columns = [];

    /** @var FormInterface */
    protected $form;

    /** @var FormView */
    protected $formView;

    /** @var array */
    protected $actions = [];

    /** @var array */
    protected $submitButton = [];

    /** @var array */
    protected $resetButton = [];

    /**
     * @param string $code
     * @param array  $configuration
     *
     * @throws \Symfony\Component\PropertyAccess\Exception\ExceptionInterface
     * @throws \TypeError
     */
    public function __construct(string $code, array $configuration)
    {
        $this->code = $code;
        /** @var array $columns */
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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return QueryHandlerInterface
     */
    public function getQueryHandler(): QueryHandlerInterface
    {
        return $this->queryHandler;
    }

    /**
     * @param QueryHandlerInterface $queryHandler
     */
    public function setQueryHandler(QueryHandlerInterface $queryHandler): void
    {
        $this->queryHandler = $queryHandler;
    }

    /**
     * @return string
     */
    public function getFormTheme(): string
    {
        return $this->formTheme;
    }

    /**
     * @param string $formTheme
     */
    public function setFormTheme(string $formTheme = null): void
    {
        $this->formTheme = $formTheme;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
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
     * @return RenderableInterface
     */
    public function getRenderer(): RenderableInterface
    {
        return $this->renderer;
    }

    /**
     * @param RenderableInterface $renderer
     */
    public function setRenderer(RenderableInterface $renderer): void
    {
        $this->renderer = $renderer;
    }

    /**
     * @return Column[]
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @param Column $column
     */
    public function addColumn(Column $column): void
    {
        $this->columns[] = $column;
    }

    /**
     * @param Column[] $columns
     */
    public function setColumns(array $columns): void
    {
        $this->columns = $columns;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param string $action
     *
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    public function getAction($action): array
    {
        if (!$this->hasAction($action)) {
            throw new \UnexpectedValueException("No action with code: '{$action}'");
        }

        return $this->actions[$action];
    }

    /**
     * @param string $action
     *
     * @return bool
     */
    public function hasAction(string $action): bool
    {
        return array_key_exists($action, $this->actions);
    }

    /**
     * @param string $action
     * @param array  $configuration
     */
    public function setAction(string $action, array $configuration): void
    {
        $this->actions[$action] = $configuration;
    }

    /**
     * @param array $actions
     */
    public function setActions(array $actions): void
    {
        $this->actions = $actions;
    }

    /**
     * @return array
     */
    public function getSubmitButton(): array
    {
        return $this->submitButton;
    }

    /**
     * @param array $submitButton
     */
    public function setSubmitButton(array $submitButton): void
    {
        $this->submitButton = $submitButton;
    }

    /**
     * @return array
     */
    public function getResetButton(): array
    {
        return $this->resetButton;
    }

    /**
     * @param array $resetButton
     */
    public function setResetButton(array $resetButton): void
    {
        $this->resetButton = $resetButton;
    }

    /**
     * @throws \LogicException
     *
     * @return FormInterface
     */
    public function getForm(): FormInterface
    {
        if (!$this->form) {
            throw new \LogicException('You must first call buildForm()');
        }

        return $this->form;
    }

    /**
     * @throws \LogicException
     *
     * @return FormView
     */
    public function getFormView(): FormView
    {
        if (!$this->formView) {
            $this->formView = $this->getForm()->createView();
        }

        return $this->formView;
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @throws \Exception
     *
     * @return FormInterface
     */
    public function buildForm(FormBuilderInterface $builder): FormInterface
    {
        $this->buildFilterActions($builder);
        $this->buildDataGridActions($builder);

        $this->form = $this->getQueryHandler()->buildForm($builder);

        return $this->form;
    }

    /**
     * @param Request $request
     *
     * @throws \Exception
     */
    public function handleRequest(Request $request): void
    {
        $this->queryHandler->handleRequest($request);
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return array|\Traversable
     */
    public function getPager()
    {
        return $this->getQueryHandler()->getPager();
    }

    /**
     * @param string $action
     * @param array  $parameters
     *
     * @throws \UnexpectedValueException
     */
    public function setActionParameters($action, array $parameters): void
    {
        if ($action === 'submit_button') {
            $this->setSubmitButton(
                array_merge(
                    $this->getSubmitButton(),
                    [
                        'route_parameters' => $parameters,
                    ]
                )
            );

            return;
        }
        if ($action === 'reset_button') {
            $this->setResetButton(
                array_merge(
                    $this->getResetButton(),
                    [
                        'route_parameters' => $parameters,
                    ]
                )
            );

            return;
        }
        $this->setAction(
            $action,
            array_merge(
                $this->getAction($action),
                [
                    'route_parameters' => $parameters,
                ]
            )
        );
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @throws \Exception
     */
    protected function buildFilterActions(FormBuilderInterface $builder): void
    {
        if (\count($this->getQueryHandler()->getConfiguration()->getFilters()) > 0) {
            $this->buildResetAction($builder);
            $this->buildSubmitAction($builder);
        }
    }

    /**
     * @param FormBuilderInterface $builder
     *
     * @throws \Exception
     */
    protected function buildResetAction(FormBuilderInterface $builder): void
    {
        $action = $builder->getOption('action');
        $defaults = [
            'form_type' => LinkType::class,
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
     * @param FormBuilderInterface $builder
     *
     * @throws \Exception
     */
    protected function buildSubmitAction(FormBuilderInterface $builder): void
    {
        $defaults = [
            'form_type' => SubmitType::class,
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
     * @param FormBuilderInterface $builder
     */
    protected function buildDataGridActions(FormBuilderInterface $builder): void
    {
        $actionsBuilder = $builder->create(
            'actions',
            FormType::class,
            [
                'label' => false,
            ]
        );
        foreach ($this->getActions() as $code => $options) {
            $type = empty($options['form_type']) ? LinkType::class : $options['form_type'];
            unset($options['form_type']);
            $actionsBuilder->add($code, $type, $options);
        }
        $builder->add($actionsBuilder);
    }

    /**
     * @param string $key
     * @param array  $columnConfiguration
     *
     * @throws \Symfony\Component\PropertyAccess\Exception\ExceptionInterface
     * @throws \TypeError
     */
    protected function createColumn(string $key, array $columnConfiguration): void
    {
        $this->columns[] = new Column($key, $this, $columnConfiguration);
    }
}
