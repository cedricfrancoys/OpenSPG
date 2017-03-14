<?php

namespace ManagementBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use UserBundle\Form\BaseUserType;

class UserType extends BaseUserType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $options['data-path'] = 'management_user_index';

        parent::buildForm($builder, $options);
    }
}
