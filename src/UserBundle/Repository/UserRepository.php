<?php

namespace UserBundle\Repository;

/**
 * UserRepository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
	public function findUsersByRole($role, $node, $makeSureFieldIsNotNull = false)
	{
	    $sql = $this->orm
	      ->getRepository('UserBundle:User')
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
}
