<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use ManagementBundle\Form\MemberType;

class ConsumerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('User', MemberType::class, array(
                'label' => false,
                'translation_domain' => 'user'
            ))
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages'
            ))
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
	        'data_class' => 'ConsumerBundle\Entity\Member',
            'translation_domain' => 'consumer'
	    ));
	}
}