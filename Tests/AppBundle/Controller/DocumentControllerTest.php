<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/documentos/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testDownload()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/documentos/declaracion_compromiso.pdf');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
