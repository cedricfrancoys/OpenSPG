<?php

namespace MediaBundle\Naming;

use Vich\UploaderBundle\Naming\OrignameNamer;
use Vich\UploaderBundle\Util\Transliterator;
use Vich\UploaderBundle\Mapping\PropertyMapping;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
* 
*/
class MediaNamer extends OrignameNamer
{
	
	public function name($object, PropertyMapping $mapping)
    {
        $file = $mapping->getFile($object);
        $name = $file->getClientOriginalName();

        $object->setFilename($name);

        /** @var $file UploadedFile */

        return parent::name($object, $mapping);
    }
}