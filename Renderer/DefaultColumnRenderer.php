<?php

namespace Sidus\DataGridBundle\Renderer;

use Sidus\DataGridBundle\Model\Column;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Render values inside the Twig engine
 */
class DefaultColumnRenderer implements ColumnRendererInterface
{
    /** @var TranslatorInterface */
    protected $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Column $column
     *
     * @return string
     */
    public function renderColumnLabel(Column $column): string
    {
        $label = $column->getLabel();
        if (!$label) {
            $key = "datagrid.{$column->getDataGrid()->getCode()}.{$column->getCode()}";
            if ($this->translator instanceof TranslatorBagInterface && $this->translator->getCatalogue()->has($key)) {
                $label = $key;
            } else {
                $label = ucfirst(
                    strtolower(trim(preg_replace(['/([A-Z])/', '/[_\s]+/'], ['_$1', ' '], $column->getCode())))
                );
            }
        }

        return $this->translator->trans($label);
    }
}
