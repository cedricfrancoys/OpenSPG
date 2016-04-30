<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testLegal()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/legal');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testCookies()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/politica_de_cookies');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAbout()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/el_spg');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
