<?php

namespace ProducerBundle\Repository;

/**
 * StockRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class StockRepository extends \Doctrine\ORM\EntityRepository
{
    public function findbyUser(\FOS\UserBundle\Model\UserInterface $user)
    {
        $q = $this->createQuery(
            'SELECT
				s
			FROM
				ProducerBundle:Stock s
			LEFT JOIN
				s.Producer p
			WHERE
				p.User = :user'
        );
        $q->setParameter('user', $user);

        return $q->getResult();
    }

    public function getNewest()
    {
        return $this
            ->createQueryBuilder('s')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
}
