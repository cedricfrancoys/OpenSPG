<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VisitProductionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array(
                'label' => false,
                'attr' => array(
                    'class' => 'fld-name',
                ),
            ))
            ->add('estimatedYield', null, array(
                'label' => false,
                'attr' => array(
                    'class' => 'fld-estimatedYield',
                ),
            ))
            ->add('originLocal', null, array(
                'label' => false,
                'attr' => array(
                    'class' => 'fld-originLocal',
                ),
                'required' => false,
            ))
            ->add('originComercial', null, array(
                'label' => false,
                'required' => false,
            ))
            ->add('originOwn', null, array(
                'label' => false,
                'required' => false,
            ))
            ->add('originBought', null, array(
                'label' => false,
                'required' => false,
            ))
            ->add('originReference', null, array(
                'label' => false,
                'attr' => array(
                    'class' => 'fld-originReference',
                ),
                'required' => false,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProducerBundle\Entity\VisitProduction',
            'translation_domain' => 'visit',
        ));
    }
}
