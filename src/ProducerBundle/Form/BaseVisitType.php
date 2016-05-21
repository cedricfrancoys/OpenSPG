<?php

namespace ProducerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

use ManagementBundle\Form\VisitProductionType;

class BaseVisitType extends AbstractType
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
            ->add('didFertilize', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('fertlizeWith', null, array(
                'attr' => array(
                    'class' => 'visit_didFertilize_yes'
                )
            ))
            ->add('fertilizeQty', null, array(
                'attr' => array(
                    'class' => 'visit_didFertilize_yes'
                )
            ))
            ->add('fertilizeOrigin', null, array(
                'attr' => array(
                    'class' => 'visit_didFertilize_yes'
                )
            ))
            ->add('usesFoliarFertilizer', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('usesFoliarFertilizerWhich', null, array(
                'attr' => array(
                    'class' => 'visit_usesFoliarFertilizer_yes'
                )
            ))
            ->add('fertilizerObservations')
            ->add('Production', CollectionType::class, array(
                'label' => false,
                'entry_type' => VisitProductionType::class,
                'allow_add' => true,
                'by_reference' => false,
                'attr' => array(
                    'class' => 'form-inline visitProduction',
                    'data-header' => '<table class="table table-bordered header">
                    <tr>
                        <td rowspan="2" style="width:240px;">Cultivos de la propiedad<br/>(nombre de la variedad)</td>
                        <td rowspan="2" style="width:200px;">Estimado de producción<br/>(especificar unidades)</td>
                        <td colspan="5">Origen de semillas o plantones</td>
                    </tr>
                    <tr>
                        <td style="width:110px;">Variedad local</td>
                        <td style="width:140px;">Variedad comercial</td>
                        <td style="width:60px;">Propia</td>
                        <td style="width:80px;">Comprada</td>
                        <td>Referencia</td>
                    </tr>
                </table>'
                )
            ))
            ->add('productionOberservation')
            // ->add('doesSoilConservation')
            ->add('scGreenManure', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('scMulching', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('scNotPlow', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('scHerbsState', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('scHerbsDistribution', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('scHerbsControl', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('scOberservations', null, array(
                'required' => false
            ))
            ->add('pcPests', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('pcControl', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'visit_pcPests_yes'
                )
            ))
            ->add('pcPrevention', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'visit_pcPests_yes'
                )
            ))
            ->add('pcOberservations', null, array(
                'required' => false
            ))
            ->add('pcPestsCrops', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'visit_pcPests_yes'
                )
            ))
            ->add('pcPestsDamage', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'visit_pcPests_yes'
                )
            ))
            ->add('doesAssociations', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('associations', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'visit_doesAssociations_yes'
                )
            ))
            ->add('doesRotations', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('rotations', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'visit_doesRotations_yes'
                )
            ))
            ->add('steepBankStatus', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Correcto' => true,
                    'incorrecto' => false
                )
            ))
            ->add('steepBankStatusReason', null, array(
                'required' => false
            ))
            ->add('hedgesBarriersExists', ChoiceType::class, array(
                'required' => false,
                'expanded' => true,
                'multiple' => false,
                'choices' => array(
                    'Si' => true,
                    'No' => false
                )
            ))
            ->add('hedgesBarriersExistsReason', null, array(
                'required' => false,
                'attr' => array(
                    'class' => 'visit_hedgesBarriersExists_yes'
                )
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
            ->add('document', FileType::class, array(
                'file_path' => 'getWebPath',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'ProducerBundle\Entity\Visit',
            'translation_domain' => 'visit',
            'validation_groups' => function (FormInterface $form) {
                $data = $form->getData();

                if (null !== $data->getAccepted()) {
                    return array('completed','default');
                }

                return array('default');
            }
	    ));
	}
}