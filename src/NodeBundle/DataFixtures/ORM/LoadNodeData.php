<?php

namespace NodeBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use NodeBundle\Entity\Node;

class LoadNodeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $node = new Node();
        $node->setName('Grupo Alpujarra Oriental');
        $node->setAddress('Cadiar');

        $manager->persist($node);

        $node2 = new Node();
        $node2->setName('Grupo Alpujarra Occidental');
        $node2->setAddress('Órgiva');

        $manager->persist($node2);

        $node3 = new Node();
        $node3->setName('Grupo Costa Granadina');
        $node3->setAddress('Gualchos');

        $manager->persist($node3);

        $manager->flush();

        $this->addReference('node', $node);
    }

    public function getOrder()
    {
        return 10;
    }
}
