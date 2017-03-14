<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('name')
            ->add('surname')
            ->add('phone')
            ->add('email')
            ->add('image', null, array(
                'image_path' => 'webPath',
                'allow_remove' => false,
                'required' => false,
            ))
            ->add('receiveEmailNewProducer', null, array(
                'required' => false,
            ))
            ->add('receiveEmailNewConsumer', null, array(
                'required' => false,
            ))
            ->add('receiveEmailNewVisit', null, array(
                'required' => false,
            ))
            ->add('receiveEmailCompletedVisit', null, array(
                'required' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
            'translation_domain' => 'user',
        ));
    }
}
