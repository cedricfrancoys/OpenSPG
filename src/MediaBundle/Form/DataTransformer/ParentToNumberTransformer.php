<?php

namespace MediaBundle\Form\DataTransformer;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use MediaBundle\Entity\Media;

class ParentToNumberTransformer implements DataTransformerInterface
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Transforms an object (parent) to a string (number).
     *
     * @param  Media|null $media
     * @return string
     */
    public function transform($media)
    {
        if (null === $media) {
            return '';
        }

        return $media->getId();
    }

    /**
     * Transforms a string (number) to an object (media).
     *
     * @param  string $mediaNumber
     * @return Media|null
     * @throws TransformationFailedException if object (media) is not found.
     */
    public function reverseTransform($mediaNumber)
    {
        // no issue number? It's optional, so that's ok
        if (!$mediaNumber) {
            return;
        }

        $media = $this->manager
            ->getRepository('MediaBundle:Media')
            // query for the media with this id
            ->find($mediaNumber)
        ;

        if (null === $media) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An media with number "%s" does not exist!',
                $mediaNumber
            ));
        }

        return $media;
    }
}