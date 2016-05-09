<?php

namespace Tests\UserBundle\Manager;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

class UserManagerTest extends KernelTestCase
{
    private $container;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }

    // public function testGetUsersByRole()
    // {
    //     $userRepository = $this->getUserRepository();
    //     $entityManager = $this->getEntityManager($userRepository);
    //     $queryBuilder = $this->getQueryBuilder();

    //     $entityManager->expects($this->once())
    //     	->method('getRepository')
    //     	->with('UserBundle:User')
    //     	->will($this->returnValue($userRepository));

    //     $userRepository->expects($this->once())
    //        ->method('createQueryBuilder')
    //        ->with('u')
    //        ->will($this->returnValue($queryBuilder));
        
    //     $this->container->set('doctrine.orm.entity_manager', $entityManager);

    //     $manager = $this->container->get('users.manager.user');

    //     $users = $manager->getUsersByRole();


    //     $this->assertEquals(200, $client->getResponse()->getStatusCode());
    // }

    private function getUserRepository()
    {
    	$userRepository = $this
            ->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        return $userRepository;
    }

    private function getEntityManager($userRepository)
    {
    	$entityManager = $this
            ->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function getQueryBuilder()
    {
    	$queryBuilder = $this
    		->getMockBuilder(QueryBuilder::class)
           ->disableOriginalConstructor()
           ->getMock();
    }
}
