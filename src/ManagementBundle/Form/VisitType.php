<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class VisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visitDate', 'date', array(
                'placeholder' => 'Not yet defined',
                'required' => false
            ))
            ->add('Participants', EntityType::class, array(
                'class' => 'UserBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u');
                },
                'multiple' => true,
                'required' => false
            ))
            ->add('startTime', 'time', array(
                'placeholder' => 'Not yet defined',
                'required' => false
            ))
            ->add('endTime', 'time', array(
                'placeholder' => 'Not yet defined',
                'required' => false
            ))
            ->add('Producer', EntityType::class, array(
                'class' => 'ProducerBundle:Member',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m');
                },
                'placeholder' => 'Choose a producer'
            ))
            ->add('Property', EntityType::class, array(
                'class' => 'ProducerBundle:Property',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.name', 'ASC');
                },
                'placeholder' => 'Choose a property'
            ))
            ->add('didFertilize')
            ->add('fertlizeWith')
            ->add('fertilizeQty')
            ->add('fertilizeOrigin')
            ->add('fertilizerObservations')
            ->add('doesSoilConservation')
            ->add('scGreenManure')
            ->add('scMulching')
            ->add('scNotPlow')
            ->add('scHerbsState')
            ->add('scHerbsDistribution')
            ->add('scHerbsControl')
            ->add('scOberservations', null, array(
                'required' => false
            ))
            ->add('pcPests')
            ->add('pcControl', null, array(
                'required' => false
            ))
            ->add('pcPrevention', null, array(
                'required' => false
            ))
            ->add('pcOberservations', null, array(
                'required' => false
            ))
            ->add('pcPestsCrops', null, array(
                'required' => false
            ))
            ->add('pcPestsDamage', null, array(
                'required' => false
            ))
            ->add('pruningRests', null, array(
                'required' => false
            ))
            ->add('agroquimicPresence')
            ->add('plasticWaste')
            ->add('agroquimicPackaging')
            ->add('pesticideSupsition')
            ->add('observations', null, array(
                'required' => false
            ))
            ->add('accepted', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Accepted' => true,
                    'Rejected' => false
                )
            ))
            ->add('save', SubmitType::class, array(
                'translation_domain' => 'messages',
                'attr' => array('btn'=>'buttons')
            ))
            ->add('saveAndClose', SubmitType::class, array(
                'translation_domain' => 'messages',
                'attr' => array('btn'=>'buttons')
            ))
            ->add('cancel', ResetType::class, array(
                'translation_domain' => 'messages',
                'attr' => array(
                    'btn' => 'buttons',
                    'class' => 'btn-danger cancel-btn',
                    'data-path' => 'manager_visit_index'
                ),
                'label' => 'Close'
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'ProducerBundle\Entity\Visit',
            'translation_domain' => 'visit'
	    ));
	}
}