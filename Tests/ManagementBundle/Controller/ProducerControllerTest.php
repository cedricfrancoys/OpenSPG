<?php

namespace Tests\ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProducerControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/gestion/producer/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
