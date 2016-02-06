<?php

namespace Sidus\DataGridBundle\Templating;

use Doctrine\Common\Collections\Collection;
use Sidus\DataGridBundle\Model\Column;
use IntlDateFormatter;
use NumberFormatter;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFunction;

class TwigRenderer extends Twig_Extension implements Renderable
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
     * @return array
     */
    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('render_object_value', [$this, 'renderObjectValue'], ['is_safe' => ['html']]),
            new Twig_SimpleFunction('render_value', [$this, 'renderValue'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * @param $object
     * @param Column $column
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function renderObjectValue($object, Column $column, array $options = [])
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        $value = $accessor->getValue($object, $column->getPropertyPath());
        return $column->renderValue($value, $options);
    }

    /**
     * @param mixed $value
     * @param array $options
     * @return string
     * @throws \Exception
     */
    public function renderValue($value, array $options = [])
    {
        if ($value instanceof \DateTime) {
            if (!empty($options['date_format'])) {
                return $value->format($options['date_format']);
            }
            $dateType = IntlDateFormatter::MEDIUM;
            $timeType = IntlDateFormatter::SHORT;
            if (array_key_exists('date_type', $options) && $options['date_type'] !== null && $options['date_type'] !== '') {
                $dateType = $options['date_type'];
            }
            if (array_key_exists('time_type', $options) && $options['time_type'] !== null && $options['time_type'] !== '') {
                $dateType = $options['time_type'];
            }
            $dateFormatter = new IntlDateFormatter($this->translator->getLocale(), $dateType, $timeType);
            return $dateFormatter->format($value);
        }
        if (is_int($value)) {
            return $value;
        }
        if (is_float($value)) {
            if (array_key_exists('decimals', $options) || array_key_exists('dec_point', $options) ||
                array_key_exists('thousands_sep', $options)
            ) {
                $decimals = array_key_exists('decimals', $options) ? $options['decimals'] : 2;
                $decPoint = array_key_exists('dec_point', $options) ? $options['dec_point'] : '.';
                $thousandsSep = array_key_exists('thousands_sep', $options) ? $options['thousands_sep'] : ',';
                return number_format($value, $decimals, $decPoint, $thousandsSep);
            }
            $numberFormatter = new NumberFormatter($this->translator->getLocale());
            return $numberFormatter->format($value);
        }
        if (is_array($value) || $value instanceof \Traversable) {
            $items = [];
            foreach ($value as $item) {
                $items[] = $this->renderValue($item, $options);
            }
            $glue = ', ';
            if (!empty($options['array_glue'])) {
                $glue = $options['array_glue'];
            }
            return implode($glue, $items);
        }
        if (is_callable($value)) {
            return $value($options);
        }
        if (is_bool($value)) {
            return $this->translator->trans($value ? 'sidus.datagrid.boolean.yes' : 'sidus.datagrid.boolean.no');
        }
        return $value;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'sidus_datagrid';
    }
}