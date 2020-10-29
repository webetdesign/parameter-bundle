<?php

namespace WebEtDesign\ParameterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterValueType extends AbstractType
{
    private string $formType = 'text';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->formType = $options['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'compound' => false,
            'type'     => 'text',
        ]);
    }

    public function getBlockPrefix()
    {
        return 'parameters_' . $this->formType;
    }
}
