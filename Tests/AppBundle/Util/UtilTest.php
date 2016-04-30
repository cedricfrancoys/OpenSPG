<?php

namespace Tests\AppBundle\Util;


use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use AppBundle\Util\Util;
use AppBundle\Entity\Contact;

class UtilTest extends KernelTestCase
{
    private $container;

    public function setUp()
    {
        self::bootKernel();

        $this->container = self::$kernel->getContainer();
    }

    public function testSendReceivedMail()
    {
        $contact = new Contact();

        $translator = $this->container->get('translator');
        $mailer = $this->container->get('mailer');
        $twig = $this->container->get('twig');

        $result = Util::sendReceivedMail($contact, $translator, $mailer, $twig);

        $this->assertTrue($result);
    }
}