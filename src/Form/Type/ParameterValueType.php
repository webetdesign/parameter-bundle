<?php

namespace WebEtDesign\ParameterBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParameterValueType extends AbstractType
{
    private string $formType = 'text';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->formType = $options['type'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
            'type'     => 'text',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'parameters_' . $this->formType;
    }
}
