<?php

namespace ProducerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;

class RejectApprovalType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reason')
            ->add('submit', SubmitType::class, array(
                'translation_domain' => 'messages',
                'attr' => array('btn' => 'buttons'),
            ))
            ->add('cancel', ResetType::class, array(
                'translation_domain' => 'messages',
                'attr' => array(
                    'btn' => 'buttons',
                    'class' => 'btn-danger cancel-btn',
                    'data-path' => 'producer_visitpublic_index',
                ),
                'label' => 'Close',
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProducerBundle\Entity\RejectApproval',
            'translation_domain' => 'visit',
        ));
    }
}
