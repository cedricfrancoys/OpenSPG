<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use ProducerBundle\Form\BaseVisitType;

class VisitType extends BaseVisitType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'attr' => array('btn' => 'buttons'),
            ))
            ->add('saveAndClose', SubmitType::class, array(
                'translation_domain' => 'messages',
                'attr' => array('btn' => 'buttons'),
            ))
            ->add('cancel', ResetType::class, array(
                'translation_domain' => 'messages',
                'attr' => array(
                    'btn' => 'buttons',
                    'class' => 'btn-danger cancel-btn',
                    'data-path' => 'management_visit_index',
                ),
                'label' => 'Close',
            ))
        ;
    }
}
