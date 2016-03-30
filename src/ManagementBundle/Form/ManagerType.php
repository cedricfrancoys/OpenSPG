<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use ManagementBundle\Form\MemberType;

class ManagerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('name')
            ->add('surname')
            ->add('phone')
            ->add('email')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $user = $event->getData();
            $form = $event->getForm();

            if( !$user || null === $user->getId() ){
                $form->add('password', 'password');
            }

            $form->add('enabled', null, array(
                'required' => false
            ));

            if( !$user || null === $user->getId() ){
                $form->add('sendEmail', 'checkbox', array(
                    'label' => 'Send Email',
                    'mapped' => false,
                    'required' => false
                ));
            }


            $form->add('save', SubmitType::class, array(
                'translation_domain' => 'messages'
            ));
        });
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'UserBundle\Entity\User',
            'translation_domain' => 'management'
	    ));
	}
}