<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Doctrine\ORM\EntityManager;

class EntityToPropertyTransformer implements DataTransformerInterface
{
    protected $em;
    protected $className;
    protected $property;
    protected $unitOfWork;

    public function __construct(EntityManager $em, $class, $property = 'id')
    {
        $this->em = $em;
        $this->unitOfWork = $this->em->getUnitOfWork();
        $this->className = $class;
        $this->property = $property;
    }

    public function transform($entity)
    {
        if (null === $entity) {
            return null;
        }

        return !empty($this->property)
            ? PropertyAccess::createPropertyAccessor()->getValue($entity, $this->property)
            : current($this->unitOfWork->getEntityIdentifier($entity));
    }

    public function reverseTransform($value)
    {
        if ($value === '' or $value === null) {
            return null;
        }

        $repo = $this->em->getRepository($this->className);
        if (!empty($this->property)) {
            $entity = $repo->findOneBy(array($this->property => $value));
        } else {
            $entity = $repo->find($value);
        }

        return $entity;
    }
}
