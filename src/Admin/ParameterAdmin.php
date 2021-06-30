<?php

declare(strict_types=1);

namespace WebEtDesign\ParameterBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormEvents;
use WebEtDesign\ParameterBundle\Entity\Parameter;
use WebEtDesign\ParameterBundle\Form\Type\ParameterFileType;
use WebEtDesign\ParameterBundle\Form\Type\ParameterValueType;
use WebEtDesign\ParameterBundle\Model\ParameterManagerInterface;

final class ParameterAdmin extends AbstractAdmin
{
    protected ParameterManagerInterface $parameterManager;

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('code')
            ->add('label');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        unset($this->listModes['mosaic']);

        if ($this->hasAccess('edit')) {
            $actions = [
                'edit'   => [],
                'delete' => [],
            ];
        } else {
            $actions = [
                'show' => [],
            ];
        }

        $listMapper
            ->addIdentifier('code')
            ->add('type')
            ->add('label')
            ->add(
                'value',
                null,
                [
                    'template' => '@WebEtDesignParameter/Admin/list__value.html.twig',
                ]
            )
            ->add(
                '_action',
                null,
                [
                    'actions' => $actions,
                ]
            );
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $options = [];
        if ($this->isCurrentRoute('edit')) {
            $options = [
                'disabled' => true,
                'required' => false,
            ];
        }

        $formMapper
            ->with('Info', ['class' => 'col-md-3'])
            ->add('code', null, $options)
            ->add(
                'type',
                ChoiceType::class,
                array_merge(
                    $options,
                    [
                        'choices' => array_flip($this->parameterManager->getTypes()),
                    ],
                )
            )
            ->add('deletable', null, $options)
            ->end();

        $subject = $this->getSubject();
        if ($subject instanceof Parameter && $this->isCurrentRoute('edit')) {
            $formMapper
                ->with('Configuration', ['class' => 'col-md-9'])
                ->add('label')
                ->ifTrue($subject->getType() === 'file')
                ->add(
                    'file',
                    ParameterFileType::class,
                    [
                        'required' => false,
                        'mapped' => false,
                        'label' => 'Fichier',
                        'constraints' => [
                            new File([
                                'maxSize' => '5m',
                                'mimeTypes' => [
                                    'application/pdf',
                                    'application/x-pdf',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid PDF document',
                            ])
                        ],
                    ]
                )
                ->ifEnd()
                ->ifTrue($subject->getType() === 'boolean')
                ->add(
                    'value',
                    CheckboxType::class,
                    [
                        'required' => false,
                        'value'    => $subject->getValue(),
                    ]
                )
                ->ifEnd()
                ->ifFalse($subject->getType() === 'file' || $subject->getType() === 'boolean')
                ->add(
                    'value',
                    ParameterValueType::class,
                    [
                        'type' => $subject->getType(),
                    ]
                )
                ->ifEnd()
                ->end();
        } else {
            $formMapper
                ->with('Configuration', ['class' => 'col-md-9'])
                ->add(
                    'config',
                    null,
                    [
                        'label' => false,
                    ]
                )
                ->end();
        }
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('code')
            ->add('label')
            ->add('value');
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection
            ->remove('export')
            ->remove('batch');
    }

    public function getBatchActions(): array
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    public function setParameterManager(ParameterManagerInterface $parameterManager): void
    {
        $this->parameterManager = $parameterManager;
    }
}
