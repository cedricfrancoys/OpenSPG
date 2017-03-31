<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\Type\TypeaheadType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Group', TypeaheadType::class, array(
                'url' => $options['group_url'],
                'create_url' => $options['group_create_url']
            ))
            ->add('Family', TypeaheadType::class, array(
                'url' => $options['family_url'],
                'dependency' => $options['family_dependency']
            ))
            ->add('Variety')
            ->add('name')
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
                    'data-path' => 'manager_product_index',
                ),
                'label' => 'Close',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProductBundle\Entity\Product',
            'translation_domain' => 'product',
            'family_dependency' => false,
            'group_create_url' => false
        ));

        $resolver->setRequired(array('group_url','family_url'));
    }
}
