<?php

namespace ProductBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use ProductBundle\Entity\Family;

class FamilyTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (Family) to a string.
     *
     * @param Family|null $group
     *
     * @return string
     */
    public function transform($family)
    {
        if (null === $family) {
            return '';
        }

        return $family->getName();
    }

    /**
     * Transforms a string to an object (Family).
     *
     * @param string $Family name
     *
     * @return Family|null
     *
     * @throws TransformationFailedException if object (Family) is not found
     */
    public function reverseTransform($name)
    {
        // no name? It's optional, so that's ok
        if (!$name) {
            return;
        }

        $Family = $this->manager
            ->getRepository('ProductBundle:Family')
            // query for the issue with this id
            ->findOneByName($name)
        ;

        if (null === $Family) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An family with name "%s" does not exist!',
                $name
            ));
        }

        return $Family;
    }
}
