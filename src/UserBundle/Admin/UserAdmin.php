<?php

namespace UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use UserBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserAdmin extends Admin
{
    public function toString($object)
    {
        return $object instanceof User
            ? $object->getUsername()
            : 'User'; // shown in the breadcrumb on the create view
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $formMapper->add('username', 'text')
            ->add('email', 'text');
        
        if (!$subject->getId()) {
            $formMapper->add('password', 'password');
        }else {
            $formMapper->add('password', 'password',array(
                'required' => false
                ));
        }

        $roles = $subject->getRoles();

        $formMapper
            ->add('ROLE_ADMIN', 'checkbox', array(
                'mapped' => false,
                'label' => 'Admin',
                'data' => in_array('ROLE_ADMIN', $roles),
                'required' => false
            ))
            ->add('ROLE_PRODUCER', 'checkbox', array(
                'mapped' => false,
                'label' => 'Producer',
                'data' => in_array('ROLE_PRODUCER', $roles),
                'required' => false
            ))
            ->add('ROLE_CONSUMER', 'checkbox', array(
                'mapped' => false,
                'label' => 'Consumer',
                'data' => in_array('ROLE_CONSUMER', $roles),
                'required' => false
            ))
            ->add('ROLE_MANAGEMENT', 'checkbox', array(
                'mapped' => false,
                'label' => 'Management',
                'data' => in_array('ROLE_MANAGEMENT', $roles),
                'required' => false
            ));
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('username');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('username');
    }

    public function prePersist($data)
    {
        $roles = array();
        ($this->getForm()->get('ROLE_ADMIN')->getData()) ? $roles[] = 'ROLE_ADMIN' : false;
        ($this->getForm()->get('ROLE_PRODUCER')->getData()) ? $roles[] = 'ROLE_PRODUCER' : false;
        ($this->getForm()->get('ROLE_CONSUMER')->getData()) ? $roles[] = 'ROLE_CONSUMER' : false;
        ($this->getForm()->get('ROLE_MANAGEMENT')->getData()) ? $roles[] = 'ROLE_MANAGEMENT' : false;
        $data->setRoles($roles);
    }
    public function preUpdate($data)
    {
        $roles = array();
        ($this->getForm()->get('ROLE_ADMIN')->getData()) ? $roles[] = 'ROLE_ADMIN' : false;
        ($this->getForm()->get('ROLE_PRODUCER')->getData()) ? $roles[] = 'ROLE_PRODUCER' : false;
        ($this->getForm()->get('ROLE_CONSUMER')->getData()) ? $roles[] = 'ROLE_CONSUMER' : false;
        ($this->getForm()->get('ROLE_MANAGEMENT')->getData()) ? $roles[] = 'ROLE_MANAGEMENT' : false;
        $data->setRoles($roles);
    }
}
