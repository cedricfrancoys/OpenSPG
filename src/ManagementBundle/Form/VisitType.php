<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class VisitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visitDate', 'date')
            ->add('Participants', EntityType::class, array(
                'class' => 'UserBundle:User',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u');
                },
                'multiple' => true
            ))
            ->add('startTime', 'time')
            ->add('endTime', 'time')
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
            ->add('scOberservations')
            ->add('pcPests')
            ->add('pcControl')
            ->add('pcPrevention')
            ->add('pcOberservations')
            ->add('pcPestsCrops')
            ->add('pcPestsDamage')
            ->add('pruningRests')
            ->add('agroquimicPresence')
            ->add('plasticWaste')
            ->add('agroquimicPackaging')
            ->add('pesticideSupsition')
            ->add('observations')
            ->add('save', SubmitType::class)
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