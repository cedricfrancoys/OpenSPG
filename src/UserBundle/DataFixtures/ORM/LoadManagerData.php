<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class LoadManagerData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('manager');
        $user->setPlainPassword('managerpassword');
        $user->setEmail('manager@test.com');
        $user->setName('Manager');
        $user->setSurname('Manager');
        $user->setPhone('123 456 789');
        $user->addRole(\UserBundle\Entity\User::ROLE_MANAGER);
        $user->setEnabled(true);
        $user->setNode($this->getReference('node'));

        $manager->persist($user);
        $manager->flush();

        $this->addReference('manager', $user);
    }

    public function getOrder()
    {
        return 20;
    }
}
