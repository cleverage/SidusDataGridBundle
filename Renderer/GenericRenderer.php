<?php

namespace Sidus\DataGridBundle\Renderer;

use IntlDateFormatter;
use NumberFormatter;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Render values inside the Twig engine
 */
class GenericRenderer implements RenderableInterface
{
    /** @var TranslatorInterface */
    protected $translator;

    /** @var PropertyAccessorInterface */
    protected $accessor;

    /**
     * @param TranslatorInterface       $translator
     * @param PropertyAccessorInterface $accessor
     */
    public function __construct(TranslatorInterface $translator, PropertyAccessorInterface $accessor)
    {
        $this->translator = $translator;
        $this->accessor = $accessor;
    }

    /**
     * @param mixed $value
     * @param array $options
     *
     * @return string
     * @throws \Exception
     */
    public function renderValue($value, array $options = []): string
    {
        if ($value instanceof \DateTime) {
            if (!empty($options['date_format'])) {
                return (string) $value->format($options['date_format']);
            }
            $dateType = IntlDateFormatter::MEDIUM;
            $timeType = IntlDateFormatter::SHORT;
            if (array_key_exists('date_type', $options)
                && $options['date_type'] !== null && $options['date_type'] !== ''
            ) {
                $dateType = $options['date_type'];
            }
            if (array_key_exists('time_type', $options)
                && $options['time_type'] !== null && $options['time_type'] !== ''
            ) {
                $dateType = $options['time_type'];
            }
            if ($value->format('H:i') === '00:00') {
                $timeType = IntlDateFormatter::NONE;
            }
            $dateFormatter = new IntlDateFormatter($this->translator->getLocale(), $dateType, $timeType);

            return (string) $dateFormatter->format($value);
        }
        if (\is_int($value)) {
            return (string) $value;
        }
        if (\is_float($value)) {
            if (array_key_exists('decimals', $options) || array_key_exists('dec_point', $options) ||
                array_key_exists('thousands_sep', $options)
            ) {
                $decimals = array_key_exists('decimals', $options) ? $options['decimals'] : 2;
                $decPoint = array_key_exists('dec_point', $options) ? $options['dec_point'] : '.';
                $thousandsSep = array_key_exists('thousands_sep', $options) ? $options['thousands_sep'] : ',';

                return number_format($value, $decimals, $decPoint, $thousandsSep);
            }
            $numberFormatter = new NumberFormatter($this->translator->getLocale(), NumberFormatter::DECIMAL);

            return (string) $numberFormatter->format($value);
        }
        if (\is_array($value) || $value instanceof \Traversable) {
            $items = [];
            /** @noinspection ForeachSourceInspection */
            foreach ($value as $key => $item) {
                $rendered = $this->renderValue($item, $options);
                if (!is_numeric($key)) {
                    $rendered = $key.': '.$rendered;
                }
                $items[] = $rendered;
            }
            $glue = ', ';
            if (!empty($options['array_glue'])) {
                $glue = $options['array_glue'];
            }

            return implode($glue, $items);
        }
        if (\is_bool($value)) {
            return (string) $this->translator->trans($value ? 'sidus.datagrid.boolean.yes' : 'sidus.datagrid.boolean.no');
        }

        return (string) $value;
    }
}
