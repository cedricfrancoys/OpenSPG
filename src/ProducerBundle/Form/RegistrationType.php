<?php

namespace ProducerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use ProducerBundle\Form\PropertyType;
use UserBundle\Form\UserType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('User', UserType::class);
            ->add('name')
            ->add('surname')
            ->add('phone')
            ->add('Properties', CollectionType::class, array(
                'entry_type' => PropertyType::class,
                'allow_add'    => true
            ))
            ->add('save', SubmitType::class)
        ;
    }

    // public function getParent()
    // {
    //     return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    // }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'ProducerBundle\Entity\Member'
	    ));
	}
}