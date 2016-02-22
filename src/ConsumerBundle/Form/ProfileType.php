<?php

namespace ConsumerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use MemberBundle\Form\ProfileType as pProfileType;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Member', pProfileType::class, array(
                'label' => false
            ))
            ->add('phone')
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'ConsumerBundle\Entity\Member'
	    ));
	}
}