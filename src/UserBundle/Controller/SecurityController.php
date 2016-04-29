<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;

class SecurityController extends BaseSecurityController
{
    public function loginAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Login", $this->get("router")->generate("fos_user_security_login"));

        $response = parent::LoginAction($request);
        
        $content = $response->getcontent();
        $loginLink = $this->generateUrl('fos_user_security_login');
        $content = str_replace('<li><a href="'.$loginLink.'"', '<li class="active"><a href="'.$loginLink.'"', $content);
        $response->setContent($content);

        return $response;
    }
}