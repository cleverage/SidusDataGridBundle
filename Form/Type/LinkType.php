<?php

namespace Sidus\DataGridBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LinkType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['route'] = $options['route'];
        $view->vars['route_parameters'] = $options['route_parameters'];
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'route',
        ]);
        $resolver->setDefaults([
            'route_parameters' => [],
        ]);
        $resolver->setAllowedTypes('route_parameters', 'array');
    }


    public function getParent()
    {
        return 'button';
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