<?php

namespace ConsumerBundle\Repository;

/**
 * MemberRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MemberRepository extends \Doctrine\ORM\EntityRepository
{
	public function getUser(\FOS\UserBundle\Model\UserInterface $user)
	{
		$em = $this->getEntityManager();

		$q = $em->createQuery(
			'SELECT
				cm,
				u
			FROM
				ConsumerBundle:Member cm
			LEFT JOIN
				cm.User u
			WHERE
				cm.User = :user'
		);
		$q->setParameter('user', $user);

		return $q->getOneOrNullResult();
	}
}
