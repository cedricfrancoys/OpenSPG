<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

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
            ->add('lastAgroquimicUsage', DateType::class, array(
                'placeholder' => 'Never',
                'required' => false
            ))
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
