<?php

namespace MediaBundle\Naming;

use Vich\UploaderBundle\Naming\DirectoryNamerInterface;
use Vich\UploaderBundle\Util\Transliterator;
use Vich\UploaderBundle\Mapping\PropertyMapping;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
* 
*/
class MediaDirectoryNamer implements DirectoryNamerInterface
{
	
	public function directoryName($object, PropertyMapping $mapping)
    {
        $directory = array();

        $parent = $object->parent;
        $parent = explode('/', $parent);
        foreach ($parent as $v) {
        	if ($v) {
        		$directory[] = $v;
        	}
        }

        return '/' . join('/',$directory);
    }
}