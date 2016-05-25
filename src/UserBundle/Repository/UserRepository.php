<?php

namespace UserBundle\Repository;

/**
 * UserRepository
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
	public function getUsersByRole($role, $node, $makeSureFieldIsNotNull = false, $makeSureFieldIsNull = false)
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
	    if( $makeSureFieldIsNotNull ){
	      $sql->andWhere("u.{$makeSureFieldIsNull} IS NULL");
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

	/**
	* Returns the managers for a given Node
	*
	* @var \NodeBundle\Entity\Node $node The node object by which we will filter the Managers
	*
	* @return Doctrine\Common\Collections\ArrayCollection
	*/
	public function getNodeManagers(\NodeBundle\Entity\Node $node)
	{
		$role = \UserBundle\Entity\User::ROLE_MANAGER;
		return $this
			->createQueryBuilder('u')
		    ->select('u')
		    ->where('u.Node = :node')
		    ->andWhere('u.roles LIKE :role')
		    ->setParameter('node', $node)
		    ->setParameter('role', "%{$role}%")
		    ->getQuery()
		    ->getResult();
	}
}
