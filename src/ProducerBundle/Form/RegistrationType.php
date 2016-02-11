<?php

namespace ProducerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use ProducerBundle\Form\PropertyType;
use UserBundle\Form\UserType;
use MemberBundle\Form\MemberType as pMemberType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Member', pMemberType::class, array(
                'label' => false
            ))
            ->add('phone')
            ->add('Properties', CollectionType::class, array(
                'entry_type' => PropertyType::class,
                'allow_add'    => true
            ))
            ->add('save', SubmitType::class)
        ;

        $builder
            ->setAttribute('add_link_label', $options['add_link_label'])
        ;
    }

    public function getBlockPrefix()
    {
        return 'producerRegistration';
    }

    public function configureOptions(OptionsResolver $resolver)
	{
	    $resolver->setDefaults(array(
	        'data_class' => 'ProducerBundle\Entity\Member',
            'add_link_label' => 'Add property'
	    ));
	}

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['add_link_label'] = $options['add_link_label'];
    }
}