<?php

namespace UserBundle\Repository;

/**
 * UserRepository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
	public function getUsersByRole($role, $node, $makeSureFieldIsNotNull = false)
	{
	    $sql = $this
	      ->createQueryBuilder('u')
	      ->select('u,c,p')
	      ->leftJoin('u.Consumer', 'c')
	      ->leftJoin('u.Producer', 'p')
	      ->where('u.Node = :node')
	      ->andWhere('u.roles LIKE :role')
	      ->setParameter('node', $node)
	      ->setParameter('role', "%{$role}%");

	    if( $makeSureFieldIsNotNull ){
	      $sql->andWhere("u.{$makeSureFieldIsNotNull} IS NOT NULL");
	    }

	    $query = $sql->getQuery();
	    $users = $query->getResult();

	    return $users;
	}

	public function getAll($node)
	{
	    return $this
	      ->createQueryBuilder('u')
	      ->select('u')
	      ->where('u.Node = :node')
	      ->setParameter('node', $node)
	      ->getQuery()
	      ->getResult();
	}
}
