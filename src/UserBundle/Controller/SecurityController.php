<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AccountStatusException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SecurityController extends Controller
{
    protected $firewall_name = 'main';

    /**
     * @Route("/login")
     * @Template()
     */
    public function loginAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Login', $this->get('router')->generate('user_security_login'));

        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $csrfToken = $this->has('security.csrf.token_manager')
            ? $this->get('security.csrf.token_manager')->getToken('authenticate')->getValue()
            : null;

        return array(
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
        );
    }

    /**
     * @Route("/login/check")
     * @Method("POST")
     */
    public function checkAction(Request $request)
    {
        // try{
        //     $loginMgr = $this->get('user.manager.login');
        //     $loginMgr->logInUser($this->firewall_name, $this->getUser(), $request);

        // } catch (AccountStatusException $ex) {
        //     // We simply do not authenticate users which do not pass the user
        //     // checker (not enabled, expired, etc.).
        // }

        return new RedirectResponse($this->router->generate('app_default_index'));
    }

    /**
     * @Route("/logout")
     * @Template()
     */
    public function logoutAction(Request $request)
    {
    }
}
