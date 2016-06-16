<?php

namespace UserBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use UserBundle\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserAdmin extends AbstractAdmin
{
    protected $translationDomain = 'user';

    public function toString($object)
    {
        return $object instanceof User
            ? $object->getUsername()
            : 'User'; // shown in the breadcrumb on the create view
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $subject = $this->getSubject();

        $formMapper
            ->with('User data')
                ->add('username', 'text')
                ->add('name', 'text')
                ->add('surname', 'text')
                ->add('phone', 'text')
                ->add('email', 'email')
                ->add('Node');
        
                if (!$subject->getId()) {
                    $formMapper->add('password', 'password');
                    $formMapper->add('sendEmail', 'checkbox', array(
                        'help' => 'sendEmailHelp',
                        'mapped' => false,
                        'required' => false
                    ));
                }

        $formMapper
                ->add('enabled')
            ->end()
            ->with('Roles');

        $roles = $subject->getRoles();

        $formMapper
                ->add(\UserBundle\Entity\User::ROLE_ADMIN, 'checkbox', array(
                    'mapped' => false,
                    'label' => 'Admin',
                    'data' => in_array(\UserBundle\Entity\User::ROLE_ADMIN, $roles),
                    'required' => false
                ))
                ->add(\UserBundle\Entity\User::ROLE_PRODUCER, 'checkbox', array(
                    'mapped' => false,
                    'label' => 'Producer',
                    'data' => in_array(\UserBundle\Entity\User::ROLE_PRODUCER, $roles),
                    'required' => false
                ))
                ->add(\UserBundle\Entity\User::ROLE_CONSUMER, 'checkbox', array(
                    'mapped' => false,
                    'label' => 'Consumer',
                    'data' => in_array(\UserBundle\Entity\User::ROLE_CONSUMER, $roles),
                    'required' => false
                ))
                ->add(\UserBundle\Entity\User::ROLE_MANAGER, 'checkbox', array(
                    'mapped' => false,
                    'label' => 'Management',
                    'data' => in_array(\UserBundle\Entity\User::ROLE_MANAGER, $roles),
                    'required' => false
                ))
                ->add(\UserBundle\Entity\User::ROLE_VISITGROUP, 'checkbox', array(
                    'mapped' => false,
                    'label' => 'Visit Group',
                    'data' => in_array(\UserBundle\Entity\User::ROLE_VISITGROUP, $roles),
                    'required' => false
                ))
            ->end();
    }

    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username');
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('username')
            ->add('name')
            ->add('surname')
            ->add('phone')
            ->add('email')
            ->add('Node');
    }

    public function prePersist($data)
    {
        $roles = array();
        ($this->getForm()->get(\UserBundle\Entity\User::ROLE_ADMIN)->getData()) ? $roles[] = \UserBundle\Entity\User::ROLE_ADMIN : false;
        ($this->getForm()->get(\UserBundle\Entity\User::ROLE_PRODUCER)->getData()) ? $roles[] = \UserBundle\Entity\User::ROLE_PRODUCER : false;
        ($this->getForm()->get(\UserBundle\Entity\User::ROLE_CONSUMER)->getData()) ? $roles[] = \UserBundle\Entity\User::ROLE_CONSUMER : false;
        ($this->getForm()->get(\UserBundle\Entity\User::ROLE_MANAGER)->getData()) ? $roles[] = \UserBundle\Entity\User::ROLE_MANAGER : false;
        $data->setRoles($roles);
    }
    public function postPersist($data)
    {
        if( $this->getForm()->get('sendEmail')->getData() ){
            $this->sendPasswordEmail();
        }
    }
    public function preUpdate($data)
    {
        $roles = array();
        ($this->getForm()->get(\UserBundle\Entity\User::ROLE_ADMIN)->getData()) ? $roles[] = \UserBundle\Entity\User::ROLE_ADMIN : false;
        ($this->getForm()->get(\UserBundle\Entity\User::ROLE_PRODUCER)->getData()) ? $roles[] = \UserBundle\Entity\User::ROLE_PRODUCER : false;
        ($this->getForm()->get(\UserBundle\Entity\User::ROLE_CONSUMER)->getData()) ? $roles[] = \UserBundle\Entity\User::ROLE_CONSUMER : false;
        ($this->getForm()->get(\UserBundle\Entity\User::ROLE_MANAGER)->getData()) ? $roles[] = \UserBundle\Entity\User::ROLE_MANAGER : false;
        $data->setRoles($roles);
    }

    public function configure() {
        $this->setTemplate('edit', 'UserBundle:Admin:edit.html.twig');
    }

    protected function sendPasswordEmail(){
        $container = $this->getConfigurationPool()->getContainer();
        $trans = $container->get('translator');
        $tpl = $container->get('twig');
        $form = $this->getForm();

        $message = \Swift_Message::newInstance()
            ->setSubject($trans->trans('Your account on SPG', array(), 'user'))
            ->setFrom('mhauptma73@gmail.com')
            ->setTo($form->get('email')->getData())
            ->setBody(
                $tpl->render(
                    'UserBundle:Emails:registration.html.twig',
                    array(
                        'password' => $form->get('password')->getData(),
                        'name' => $form->get('name')->getData(),
                        'surname' => $form->get('surname')->getData(),
                        'username' => $form->get('username')->getData(),
                        'phone' => $form->get('phone')->getData(),
                        'email' => $form->get('email')->getData(),
                        'node' => $form->get('Node')->getData(),
                        'enabled' => $form->get('enabled')->getData()
                    )
                ),
                'text/html'
            )
        ;
        $container->get('mailer')->send($message);
    }
}
