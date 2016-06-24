<?php

namespace MediaBundle\Naming;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Util\Transliterator;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
* 
*/
class MediaDirectoryNamer implements DirectoryNamerInterface
{
	
	private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function directoryName($object, PropertyMapping $mapping)
    {
        $directory = $this->manager->getRepository('MediaBundle:Media')->getPathArray($object->getParent());

        return '/' . join('/',$directory);
    }
}