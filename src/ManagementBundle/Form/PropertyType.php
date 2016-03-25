<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class PropertyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('areaName')
            ->add('address')
            ->add('regNr')
            ->add('name', null, array(
                'attr' => array(
                    'class' => 'nameField'
                )
            ))
            ->add('tenure')
            ->add('size')
            ->add('previousUses')
            ->add('waterTypeOrigin')
            ->add('crops')
            ->add('certified')
            ->add('certifiedYear')
            ->add('certifiedProvider')
            ->add('lastAgroquimicUsage', 'date')
            ->add('fertilizer')
            ->add('phytosanitary')
            ->add('ownerLivesHere')
            ->add('ownerDistance')
            ->add('workforce')
            ->add('productSellingDistance')
            ->add('productSellingTime')
            ->add('productConservation')
            ->add('productConservationDetails')
            ->add('surroundings')
            ->add('surroundingProblems')
            ->add('Save', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProducerBundle\Entity\Property',
            'translation_domain' => 'property'
        ));
    }
}
