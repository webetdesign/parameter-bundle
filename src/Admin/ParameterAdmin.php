<?php

declare(strict_types=1);

namespace WebEtDesign\ParameterBundle\Admin;

use Doctrine\ORM\EntityManagerInterface;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
use WebEtDesign\MediaBundle\CMS\transformer\MediaContentTransformer;
use WebEtDesign\MediaBundle\Form\Type\WDMediaType;
use WebEtDesign\ParameterBundle\Entity\Parameter;
use WebEtDesign\ParameterBundle\Form\Type\ParameterFileType;
use WebEtDesign\ParameterBundle\Form\Type\ParameterValueType;
use WebEtDesign\ParameterBundle\Model\ParameterManagerInterface;

final class ParameterAdmin extends AbstractAdmin
{
    protected ParameterManagerInterface $parameterManager;
    protected array $types = [];

    public function __construct(
        private EntityManagerInterface $entityManager,
        private ParameterBagInterface $parameterBag,
        ?string $code = null,
        ?string $class = null,
        ?string $baseControllerName = null,
    )
    {
        $this->types = $this->parameterBag->get('parameter.fixtures');
        parent::__construct($code, $class, $baseControllerName);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('code')
            ->add('label');
    }

    protected function configureListFields(ListMapper $list): void
    {
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

        $list
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
                ListMapper::NAME_ACTIONS,
                null,
                [
                    'actions' => $actions,
                ]
            );
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $options = [];
        if ($this->isCurrentRoute('edit')) {
            $options = [
                'disabled' => true,
                'required' => false,
            ];
        }

        $form
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
            $help = null;

            if(array_key_exists($subject->getCode(), $this->types) && array_key_exists('help', $this->types[$subject->getCode()])){
                $help = $this->types[$subject->getCode()]['help'];
            }

            $form
                ->with('Configuration', ['class' => 'col-md-9'])
                    ->add('label')
                ->ifTrue($subject->getType() === 'file')
                    ->add(
                    'file',
                    ParameterFileType::class,
                    [
                        'required'    => false,
                        'mapped'      => false,
                        'label'       => 'Fichier',
                        'help' => $help,
                        'constraints' => [
                            new File([
                                'maxSize' => '15m',
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
                            'help' => $help,
                        ]
                    )
                ->ifEnd()
                ->ifTrue(
                    $subject->getType() === 'media' && class_exists('WebEtDesign\MediaBundle\Form\Type\WDMediaType')
                )
                    ->add(
                        'value',
                        WDMediaType::class,
                        [
                            'required' => false,
                            'category' => 'media_parameter',
                            'help' => $help,
                        ]
                    )
                ->ifEnd()
                ->ifTrue($subject->getType() === 'textarea')
                    ->add(
                        'value',
                        TextareaType::class,
                        [
                            'help' => $help,
                        ]
                    )
                ->ifEnd()
                ->ifTrue($subject->getType() === 'ckeditor')
                ->add(
                    'value',
                    CKEditorType::class,
                    [
                        'help' => $help,
                    ]
                )
                ->ifEnd()
                ->ifFalse(in_array($subject->getType(), ['file', 'boolean', 'media', 'textarea', 'ckeditor']))
                    ->add(
                        'value',
                        ParameterValueType::class,
                        [
                            'type' => $subject->getType(),
                            'help' => $help,
                        ]
                    )
                ->ifEnd()
            ;

            $form
                ->end();
        } else {
            $form
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

        if ($subject->getType() === 'media') {
            $form
                ->getFormBuilder()
                ->get('value')
                ->addModelTransformer(new MediaContentTransformer($this->entityManager));
        }
    }

    protected function configureShowFields(ShowMapper $showMapper): void
    {
        $showMapper
            ->add('code')
            ->add('label')
            ->add('value');
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('export')
            ->remove('batch');
    }

    protected function configureBatchActions(array $actions): array
    {
        unset($actions['delete']);

        return $actions;
    }

    public function setParameterManager(ParameterManagerInterface $parameterManager): void
    {
        $this->parameterManager = $parameterManager;
    }
}
