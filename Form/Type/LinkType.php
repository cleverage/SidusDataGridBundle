<?php

namespace Sidus\DataGridBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['route'] = $options['route'];
        $view->vars['route_parameters'] = $options['route_parameters'];
        $view->vars['uri'] = $options['uri'];
        $view->vars['icon'] = $options['icon'];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'route' => null,
            'route_parameters' => [],
            'uri' => null,
            'icon' => null,
        ]);
        $resolver->setAllowedTypes('route_parameters', 'array');
        $resolver->setNormalizer('route', function(Options $options, $value){
            if (!($value xor $options['uri'])) {
                throw new \UnexpectedValueException("You must specify either a 'route' or an 'uri' option");
            }
            return $value;
        });
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'sidus_link';
    }
}
