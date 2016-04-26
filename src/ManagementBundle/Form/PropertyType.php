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
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;

class PropertyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Member', EntityType::class, array(
                'class' => 'ProducerBundle:Member',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('m')
                        ->select('m,u')
                        ->leftJoin('m.User', 'u')
                        ->where('u.Node = :node')
                        ->andWhere('u.roles LIKE :role')
                        ->setParameter('node', $options['node'])
                        ->setParameter('role', '%ROLE_PRODUCER%')
                        ->orderBy('u.name', 'ASC');
                },
                'choice_label' => function($member){
                    return $member->getUser()->getName() . ' ' . $member->getUser()->getSurname();
                },
                'label' => 'Producer'
            ))
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
                    'data-path' => 'manager_property_index'
                ),
                'label' => 'Close'
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
            'translation_domain' => 'property',
            'node' => false
        ));
    }
}
