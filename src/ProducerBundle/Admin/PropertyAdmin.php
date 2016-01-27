<?php

namespace ProducerBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class PropertyAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('areaName')
            ->add('address')
            ->add('regNr')
            ->add('name')
            ->add('tenure')
            ->add('size')
            ->add('previousUses')
            ->add('waterTypeOrigin')
            ->add('surroundings')
            ->add('surroundingProblems')
            ->add('crops')
            ->add('certified')
            ->add('certifiedYear')
            ->add('certifiedProvider')
            ->add('lastAgroquimicUsage')
            ->add('fertilizer')
            ->add('phytosanitary')
            ->add('ownerLivesHere')
            ->add('ownerDistance')
            ->add('workforce')
            ->add('productSellingDistance')
            ->add('productSellingTime')
            ->add('productConservation')
            ->add('productConservationDetails')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('areaName')
            ->add('address')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('areaName')
            ->add('address')
            ->add('regNr')
            ->add('name')
            ->add('tenure')
            ->add('size')
            ->add('previousUses')
            ->add('waterTypeOrigin')
            ->add('surroundings')
            ->add('surroundingProblems')
            ->add('crops')
            ->add('certified')
            ->add('certifiedYear')
            ->add('certifiedProvider')
            ->add('lastAgroquimicUsage', 'sonata_type_date_picker')
            ->add('fertilizer')
            ->add('phytosanitary')
            ->add('ownerLivesHere')
            ->add('ownerDistance')
            ->add('workforce')
            ->add('productSellingDistance')
            ->add('productSellingTime')
            ->add('productConservation')
            ->add('productConservationDetails')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('areaName')
            ->add('address')
            ->add('regNr')
            ->add('name')
            ->add('tenure')
            ->add('size')
            ->add('previousUses')
            ->add('waterTypeOrigin')
            ->add('surroundings')
            ->add('surroundingProblems')
            ->add('crops')
            ->add('certified')
            ->add('certifiedYear')
            ->add('certifiedProvider')
            ->add('lastAgroquimicUsage')
            ->add('fertilizer')
            ->add('phytosanitary')
            ->add('ownerLivesHere')
            ->add('ownerDistance')
            ->add('workforce')
            ->add('productSellingDistance')
            ->add('productSellingTime')
            ->add('productConservation')
            ->add('productConservationDetails')
        ;
    }
}
