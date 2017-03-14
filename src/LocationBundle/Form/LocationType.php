<?php

namespace LocationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('LatLng', MapType::class, array(
                'default_lat' => '37.06394430056685',
                'default_lng' => '-3.09814453125',
                'map_width' => 600,
                'type' => HiddenType::class,
                'label' => false,
                'markerTitleField' => $options['markerTitleField'],
            ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'LocationBundle\Entity\Location',
            'markerTitleField' => false,
        ));
    }
}
