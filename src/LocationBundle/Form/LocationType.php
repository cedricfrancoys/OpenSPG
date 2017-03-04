<?php

namespace LocationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use mhauptma73\GoogleMapFormTypeBundle\Form\Type\GoogleMapType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('LatLng', GoogleMapType::class, array(
                'default_lat' => '37.06394430056685',
                'default_lng' => '-3.09814453125',
                'map_width' => 600,
                'type' => HiddenType::class
            ))
            ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LocationBundle\Entity\Location'
        ));
    }
}
