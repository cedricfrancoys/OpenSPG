<?php

namespace ConsumerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use UserBundle\Form\UserType;
use UserBundle\Form\RegistrationType as BaseRegistrationType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('User', BaseRegistrationType::class, array(
                'label' => false
            ))
            ->add('save', SubmitType::class)
        ;
    }

    public function getBlockPrefix()
    {
        return 'consumerRegistration';
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'ConsumerBundle\Entity\Member'
	    ));
	}
}