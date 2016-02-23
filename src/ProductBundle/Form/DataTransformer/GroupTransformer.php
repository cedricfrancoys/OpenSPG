<?php

namespace ProductBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use ProductBundle\Entity\ProductGroup;

class GroupTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (Group) to a string.
     *
     * @param  ProductGroup|null $group
     * @return string
     */
    public function transform($group)
    {
        if (null === $group) {
            return '';
        }

        return $group->getName();
    }

    /**
     * Transforms a string to an object (Group).
     *
     * @param  string $Group name
     * @return Group|null
     * @throws TransformationFailedException if object (Group) is not found.
     */
    public function reverseTransform($name)
    {
        // no name? It's optional, so that's ok
        if (!$name) {
            return;
        }

        $Group = $this->manager
            ->getRepository('ProductBundle:ProductGroup')
            // query for the issue with this id
            ->findOneByName($name)
        ;

        if (null === $Group) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An group with name "%s" does not exist!',
                $name
            ));
        }

        return $Group;
    }
}