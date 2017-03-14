<?php

namespace ProductBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use ProductBundle\Entity\Variety;

class VarietyTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (variety) to a string.
     *
     * @param variety|null $group
     *
     * @return string
     */
    public function transform($variety)
    {
        if (null === $variety) {
            return '';
        }

        return $variety->getName();
    }

    /**
     * Transforms a string to an object (variety).
     *
     * @param string $Variety name
     *
     * @return Variety|null
     *
     * @throws TransformationFailedException if object (Variety) is not found
     */
    public function reverseTransform($name)
    {
        // no name? It's optional, so that's ok
        if (!$name) {
            return;
        }

        $Variety = $this->manager
            ->getRepository('ProductBundle:Variety')
            ->findOneByName($name)
        ;

        if (null === $Variety) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'A variety with name "%s" does not exist!',
                $name
            ));
        }

        return $Variety;
    }
}
