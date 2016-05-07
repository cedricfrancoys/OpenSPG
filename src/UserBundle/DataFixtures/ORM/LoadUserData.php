<?php

namespace UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('miguel.hauptmann');
        $userAdmin->setPlainPassword('testpassword');
        $userAdmin->setEmail('testemail@test.com');
        $userAdmin->setName('Miguel');
        $userAdmin->setSurname('Hauptmann');
        $userAdmin->setPhone('123 456 789');
        $userAdmin->addRole('ROLE_ADMIN');
        $userAdmin->setEnabled(true);
        $userAdmin->setNode($this->getReference('node'));

        $manager->persist($userAdmin);
        $manager->flush();
    }

    public function getOrder()
    {
        return 20;
    }
}