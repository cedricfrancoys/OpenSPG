<?php

namespace Tests\UserBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $fixtures = $this->loadFixtures(array(
            'NodeBundle\DataFixtures\ORM\LoadNodeData',
            'UserBundle\DataFixtures\ORM\LoadManagerData'
        ));
        $repository = $fixtures->getReferenceRepository();
    }

    public function testSearchByCategoryName()
    {
        $node = $repository->getReference('node');

        $managers = $this->em
            ->getRepository('UserBundle:User')
            ->getNodeManagers($node)
        ;

        $this->assertCount(1, $managers);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}