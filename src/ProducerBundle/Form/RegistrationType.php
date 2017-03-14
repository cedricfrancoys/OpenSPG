<?php

namespace ProducerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use UserBundle\Form\RegistrationType as BaseRegistrationType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('User', BaseRegistrationType::class, array(
                'label' => false,
            ))
            // ->add('Properties', CollectionType::class, array(
            //     'entry_type' => PropertyType::class,
            //     'allow_add'    => true,
            //     'attr' => array(
            //         'data-add_link_label' => 'Add property',
            //         'data-item_label' => 'Property',
            //         'data-translation_domain' => 'property'
            //     )
            // ))
            ->add('save', SubmitType::class)
        ;
    }

    public function getBlockPrefix()
    {
        return 'producerRegistration';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProducerBundle\Entity\Member',
        ));
    }
}
