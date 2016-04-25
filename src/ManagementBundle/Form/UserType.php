<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class UserType extends AbstractType
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
            ->add('phone')
            ->add('email')
            ->add('image', FileType::class, array(
                'image_path' => 'webPath',
                'allow_remove' => false,
                'required' => false
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            if( !$user || null === $user->getId() ){
                $form->add('password', PasswordType::class);
            }

            $form->add('enabled', null, array(
                'required' => false
            ));

            if( !$user || null === $user->getId() ){
                $form->add('sendEmail', CheckboxType::class, array(
                    'label' => 'Send Email',
                    'mapped' => false,
                    'required' => false
                ));
            }


            $form->add('save', SubmitType::class, array(
                'translation_domain' => 'messages'
            ))
            ->add('cancel', ResetType::class, array(
                'translation_domain' => 'messages',
                'attr' => array(
                    'class' => 'btn-danger cancel-btn',
                    'data-path' => 'manager_user_index'
                )
            ));
        });
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'UserBundle\Entity\User',
            'translation_domain' => 'user'
        ));
    }
}
