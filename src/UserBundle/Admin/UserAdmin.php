<?php

namespace UserBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

class UserAdmin extends Admin
{
    public function toString($object)
    {
        return $object instanceof BlogPost
            ? $object->getTitle()
            : 'User'; // shown in the breadcrumb on the create view
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $formMapper->add('username', 'text')
            ->add('username_canonical', 'text')
            ->add('email', 'text')
            ->add('email_canonical', 'text');
        if (!$subject->getId()) {
            $formMapper->add('password', 'password');
        }else {
            $formMapper->add('password', 'password',array(
                'required' => false
                ));
        }
        $formMapper->add('enabled', 'checkbox')
            ->add('locked', 'checkbox')
            ->add('expired', 'checkbox');
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper->add('username');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper->addIdentifier('username');
    }
}
