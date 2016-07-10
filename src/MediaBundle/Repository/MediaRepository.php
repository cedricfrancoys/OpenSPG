<?php

namespace MediaBundle\Repository;

use Gedmo\Tree\Traits\Repository\ORM\NestedTreeRepositoryTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use MediaBundle\Entity\Media;

/**
 * MediaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MediaRepository extends \Doctrine\ORM\EntityRepository
{
	
    use NestedTreeRepositoryTrait;

    public function __construct(EntityManager $em, ClassMetadata $class)
    {
        parent::__construct($em, $class);

        $this->initializeTreeRepository($em, $class);
    }

	public function getContent($path = '', $asArray = false){
		$sql = $this
	      ->createQueryBuilder('m')
	      ->select('m')
	      ->orderBy('m.type')
	      ->addOrderBy('m.title')
	    ;

	    if ($path !== '') {
	    	$sql->where('m.media LIKE :path')
	    		->setParameter('path', $path . '/%')
	    	;
	    }else{
	    	$sql->where('m.media NOT LIKE :slash')
	    		->setParameter('slash', '%/%')
	    	;
	    }

	    $query = $sql->getQuery();
	    if ($asArray) {
	    	$items = $query->getArrayResult();
	    }else{
	    	$items = $query->getResult();
	    }

	    return $items;
	}

	public function getPathArray(Media $node)
	{
		$nodes = $this->getPath($node);

		$path = [];
		foreach ($nodes as $node) {
			$path[] = $node->getMedia();
		}

		return $path;
	}
}