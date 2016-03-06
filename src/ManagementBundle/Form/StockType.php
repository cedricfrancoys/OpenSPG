<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use ManagementBundle\Form\MemberType;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('amount')
            ->add('price')
            ->add('unit', ChoiceType::class, array(
                'choices' => array(
                    'kg' => 'kg',
                    'l' => 'liter',
                    'unit' => 'unit'
                ),
                'placeholder' => 'Please choose'
            ))
            ->add('caduce')
            ->add('isExchangeable')
            ->add('Product')
            ->add('Producer')
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'ProducerBundle\Entity\Stock',
            'translation_domain' => 'stock'
	    ));
	}
}