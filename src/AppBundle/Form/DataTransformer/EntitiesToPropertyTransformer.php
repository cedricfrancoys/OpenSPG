<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class EntitiesToPropertyTransformer extends EntityToPropertyTransformer
{
    public function transform($array)
    {
        if (null === $array || $array === '') {
            return array();
        }

        if ($array instanceof Collection) {
            $array = $array->toArray();
        }

        if (!is_array($array)) {
            throw new UnexpectedTypeException($array, 'array');
        }

        $return = array();
        foreach ($array as $entity) {
            $value = parent::transform($entity);
            if ($value !== null) {
                $return[] = $value;
            }
        }

        return $return;
    }

    public function reverseTransform($array)
    {
        if (null === $array || $array === '') {
            return array();
        }

        if (!is_array($array)) {
            throw new UnexpectedTypeException($array, 'array');
        }

        $return = new ArrayCollection();
        foreach ($array as $value) {
            $entity = parent::reverseTransform($value);
            if ($value !== null) {
                $return[] = $entity;
            }
        }

        return $return;
    }
}
