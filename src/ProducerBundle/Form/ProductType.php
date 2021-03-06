<?php

namespace ProducerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use ProductBundle\Form\DataTransformer\GroupTransformer;
use ProductBundle\Form\DataTransformer\FamilyTransformer;
use ProductBundle\Form\DataTransformer\VarietyTransformer;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Group', EntityType::class, array(
                'class' => 'ProductBundle\\Entity\\ProductGroup',
                'placeholder' => 'Please choose...'
            ))
            ->add('Family', EntityType::class, array(
                'class' => 'ProductBundle\\Entity\\Family',
                'placeholder' => 'Please choose...'
            ))
            ->add('Variety', EntityType::class, array(
                'class' => 'ProductBundle\\Entity\\Variety',
                'placeholder' => 'Please choose...',
                'required' => false
            ))
            ->add('name')
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'attr' => array('btn'=>'buttons')
            ))
            ->add('saveAndClose', SubmitType::class, array(
                'translation_domain' => 'messages',
                'attr' => array('btn'=>'buttons')
            ))
            ->add('cancel', ResetType::class, array(
                'translation_domain' => 'messages',
                'attr' => array(
                    'btn' => 'buttons',
                    'class' => 'btn-danger cancel-btn',
                    'data-path' => 'producer_stock_index'
                ),
                'label' => 'Close'
            ))
        ;

        // $builder->get('Group')
        //     ->addModelTransformer(new GroupTransformer($this->manager));
        // $builder->get('Family')
        //     ->addModelTransformer(new FamilyTransformer($this->manager));
        // $builder->get('Variety')
        //     ->addModelTransformer(new VarietyTransformer($this->manager));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProductBundle\Entity\Product',
            'translation_domain' => 'product'
        ));
    }
}
