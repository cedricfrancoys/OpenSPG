<?php

namespace ProducerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormView;

class PropertyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $year = date('Y');
        $lastTenYears = array();
        for ($i=$year; $i > $year-50 ; $i--) { 
            $lastTenYears[$i] = $i;
        }

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
            ->add('surroundings')
            ->add('surroundingProblems')
            ->add('crops')
            ->add('certified', null, array(
                'required' => false
            ))
            ->add('certifiedYears', ChoiceType::class, array(
                'choices' => $lastTenYears,
                'multiple' => true,
                'required' => false
            ))
            ->add('certifiedProvider', null, array(
                'required' => false
            ))
            ->add('lastAgroquimicUsage', 'date', array(
                'required' => false,
                'placeholder' => 'Nunca'
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
