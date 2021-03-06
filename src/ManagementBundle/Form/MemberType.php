<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use \UserBundle\Form\ProfileType as pProfileType;

class MemberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('name')
            ->add('surname')
            ->add('phone', null, array(
                'required' => false
            ))
            ->add('email', null, array(
                'required' => false
            ))
            ->add('image', FileType::class, array(
                'image_path' => 'webPath',
                'allow_remove' => false,
                'required' => false
            ));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            if( !$user || null === $user->getId() ){
                $form->add('password', 'password');
            }

            $form
                ->add('receiveEmailNewProducer', null, array(
                    'required' => false
                ))
                ->add('receiveEmailNewConsumer', null, array(
                    'required' => false
                ))
                ->add('receiveEmailNewVisit', null, array(
                    'required' => false
                ))
                ->add('receiveEmailCompletedVisit', null, array(
                    'required' => false
                ))
                ->add('enabled', null, array(
                    'required' => false
                ))
            ;

            if( !$user || null === $user->getId() ){
                $form->add('sendEmail', 'checkbox', array(
                    'label' => 'Send Email',
                    'mapped' => false,
                    'required' => false
                ));
            }
        });

    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User'
        ));
    }
}
