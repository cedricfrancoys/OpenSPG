<?php

namespace Tests\ManagementBundle\Controller;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\User as SymfonyUser;

use UserBundle\Manager\UserManager;
use UserBundle\Entity\User;

class UserControllerTest extends WebTestCase
{
    private $container;

    public function setUp()
    {
        $this->client = static::makeClient();

        $fixtures = $this->loadFixtures(array(
            'NodeBundle\DataFixtures\ORM\LoadNodeData',
            'UserBundle\DataFixtures\ORM\LoadManagerData'
        ));
        $repository = $fixtures->getReferenceRepository();
        $this->user = $repository->getReference('manager');

        $this->container = self::$kernel->getContainer();
        $this->container->set('users.manager.user', $this->getUserManagerMock());
    }

    public function testIndex()
    {
        $path = '/gestion/usuario/';
        $crawler = $this->client->request('GET', $path);

        // 302 Redirect expected (redirect to login page)
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Before log-in into "'.$path.'", it should redirect.');

        $this->loginFOS();

		$crawler = $this->client->request('GET', $path);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Access to "'.$path.'" was not successfull.');
    }

    public function testEdit()
    {
        $user_id = $this->user->getId();
        // $user_id = 1;
        $path = '/gestion/usuario/'.$user_id.'/edit';
        $crawler = $this->client->request('GET', $path);

        // 302 Redirect expected (redirect to login page)
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode(), 'Before log-in into "'.$path.'", it should redirect.');

        $this->loginFOS();

        $crawler = $this->client->request('GET', $path);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'Access to "'.$path.'" after log-in was not successfull.');
    }

    /**
     * @param array|null $roles
     * @return \Symfony\Bundle\FrameworkBundle\Client
     */
    // protected static function createAuthenticatedClient(array $roles = null) {
    //     // Assign default user roles if no roles have been passed.
    //     if($roles == null) {
    //         $role = new Role('ROLE_SUPER_ADMIN');
    //         $roles = array($role);
    //     } else {
    //         $tmpRoles = array();
    //         foreach($roles as $role)
    //         {
    //             $role = new Role($role, $role);
    //             $tmpRoles[] = $role;
    //         }
    //         $roles = $tmpRoles;
    //     }

    //     $user = new SymfonyUser('test_user', 'passwd', $roles);

    //     return self::createAuthentication(static::createClient(), $user);
    // }

    // private function login()
    // {
    //     $session = $this->client->getContainer()->get('session');

    //     $firewall = 'main';
    //     $token = new UsernamePasswordToken($this->getUserEntity(), null, $firewall, array('ROLE_MANAGEMENT'));
    //     $this->container->get('security.token_storage')->setToken($token);
    //     $session->set('_security_'.$firewall, serialize($token));
    //     $session->save();

    //     $cookie = new Cookie($session->getName(), $session->getId());
    //     $this->client->getCookieJar()->set($cookie);
    // }

    // private function loginFUM()
    // {
    //     $client = $this->client;
    //     $container = $client->getContainer();

    //     $session = $container->get('session');
    //     /** @var $userManager \FOS\UserBundle\Doctrine\UserManager */
    //     $userManager = $container->get('fos_user.user_manager');
    //     /** @var $loginManager \FOS\UserBundle\Security\LoginManager */
    //     $loginManager = $container->get('fos_user.security.login_manager');
    //     $firewallName = $container->getParameter('fos_user.firewall_name');

    //     $user = $userManager->findUserBy(array('username' => 'gestor'));
    //     $loginManager->loginUser($firewallName, $user);

    //     // save the login token into the session and put it in a cookie
    //     $container->get('session')->set('_security_' . $firewallName,
    //         serialize($container->get('security.token_storage')->getToken()));
    //     $container->get('session')->save();
    //     $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));
    // }

    private function loginFOS()
    {
        $this->loginAs($this->user, 'main');
        $this->client = static::makeClient();
    }

    // private function getUserEntity()
    // {
    // 	$user = new User();

    // 	$user->setUsername('gestor');
    // 	$user->setName('Gestor');
    // 	$user->setSurname('Gestor');
    // 	$user->addRole('ROLE_MANAGEMENT');

    // 	return $user;
    // }

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
