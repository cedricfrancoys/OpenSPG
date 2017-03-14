<?php

namespace ConsumerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\Type\HiddenEntityType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
            ))
            ->add('email', EmailType::class, array(
            ))
            ->add('message', TextareaType::class, array(
            ))
            ->add('Receiver', HiddenEntityType::class, array(
                'class' => 'UserBundle\Entity\User',
                'label' => false,
            ))
            ->add('Stock', HiddenType::class, array(
                'mapped' => false,
            ))
            ->add('send', SubmitType::class, array(
                'translation_domain' => 'contact',
                'attr' => array('btn' => 'buttons'),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\\Entity\\Contact',
            'translation_domain' => 'contact',
        ));
    }
}
