<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class FeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('User')
            ->add('code')
            ->add('name')
            ->add('startDate')
            ->add('amount')
            ->add('status', ChoiceType::class, array(
                'choices' => array(
                    'Pending' => 'pending',
                    'Paid' => 'paid'
                )
            ))
            ->add('save', SubmitType::class)
            ->add('cancel', ResetType::class, array(
                'translation_domain' => 'messages',
                'attr' => array(
                    'class' => 'btn-danger cancel-btn',
                    'data-path' => 'manager_user_index'
                )
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'FeeBundle\Entity\Fee',
            'translation_domain' => 'fee'
	    ));
	}
}