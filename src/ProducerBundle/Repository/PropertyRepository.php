<?php

namespace ProducerBundle\Repository;

/**
 * PropertyRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PropertyRepository extends \Doctrine\ORM\EntityRepository
{
	public function findbyUser(\FOS\UserBundle\Model\UserInterface $user)
	{
		$em = $this->getEntityManager();

		$q = $em->createQuery(
			'SELECT
				p
			FROM
				ProducerBundle:Property p
			LEFT JOIN
				p.Member pm
			WHERE
				pm.User = :user'
		);
		$q->setParameter('user', $user);

		return $q->getResult();
	}
}
