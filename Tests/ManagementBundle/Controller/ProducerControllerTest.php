<?php

namespace Tests\ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use UserBundle\Manager\UserManager;
use UserBundle\Entity\User;

class ProducerControllerTest extends WebTestCase
{
    private $container;

    public function setUp()
    {
        $this->client = static::createClient();

        $this->container = self::$kernel->getContainer();
        $this->container->set('users.manager.user', $this->getUserManagerMock());
    }

    public function testIndex()
    {
        $crawler = $this->client->request('GET', '/gestion/producer/');

        // 302 Redirect expected (redirect to login page)
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Before log-in into "/gestion/producer/", it should redirect.');

  //       $this->login();

		// $crawler = $this->client->request('GET', '/gestion/producer/');

  //       $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Access to "/gestion/producer/" was not successfull.');
    }

    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken($this->getUserEntity(), null, $firewall, array('ROLE_MANAGEMENT'));
        $this->container->get('security.token_storage')->setToken($token);
        $session->set('_security_'.$firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function getUserEntity()
    {
    	$user = new User();

    	$user->setUsername('producer');
    	$user->setName('Producer');
    	$user->setSurname('Producer');
    	$user->addRole('ROLE_MANAGEMENT');

    	return $user;
    }

    private function getUserManagerMock()
    {
    	$mock = $this
            ->getMockBuilder(UserManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        // $mock
        // 	->expects($this->once())
        // 	->method('getUsersByRole')
        // 	->with('ROLE_PRODUCER')
        // 	->will($this->returnValue(array()))
        // ;

        return $mock;
    }
}
