<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use ManagementBundle\Form\MemberType;

class ProducerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('User', MemberType::class, array(
                'label' => false
            ))
            ->add('activeAsProducer', null, array(
                'translation_domain' => 'producer'
            ))
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
                    'data-path' => 'manager_producer_index'
                ),
                'label' => 'Close'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'ProducerBundle\Entity\Member',
            'translation_domain' => 'user'
	    ));
	}
}