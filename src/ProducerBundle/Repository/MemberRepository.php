<?php

namespace ProducerBundle\Repository;

/**
 * MemberRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MemberRepository extends \UserBundle\Repository\MemberRepository
{
	public function getUser(\FOS\UserBundle\Model\UserInterface $user)
	{
		$em = $this->getEntityManager();

		$q = $em->createQuery(
			'SELECT
				u,
				pm
			FROM
				ProducerBundle:Member pm
			LEFT JOIN
				pm.User u
			WHERE
				pm.User = :user'
		);
		$q->setParameter('user', $user);

		return $q->getOneOrNullResult();
	}
}
