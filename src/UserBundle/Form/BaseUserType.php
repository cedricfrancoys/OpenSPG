<?php

namespace UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BaseUserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!array_key_exists('data-path', $options)) {
            throw new MissingOptionsException('The required option "data-path" is missing.');
        }

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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {

            $user = $event->getData();
            $form = $event->getForm();

            if( !$user || null === $user->getId() ){
                $form->add('password', PasswordType::class);
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
                $form->add('sendEmail', CheckboxType::class, array(
                    'label' => 'Send Email',
                    'mapped' => false,
                    'required' => false
                ));
            }


            $form
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
                        'data-path' => $options['data-path']
                    ),
                    'label' => 'Close'
                ))
            ;
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
