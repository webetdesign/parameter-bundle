<?php

namespace WebEtDesign\ParameterBundle\Form\Type;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class ParameterFileType extends FileType
{
    public function getBlockPrefix(): string
    {
        return 'parameters_file';
    }
}