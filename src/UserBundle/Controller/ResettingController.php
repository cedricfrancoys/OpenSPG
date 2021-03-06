<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\ResettingController as BaseResettingController;

class ResettingController extends BaseResettingController
{
    public function requestAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Reset password", $this->get("router")->generate("fos_user_resetting_request"));

        return parent::requestAction();
    }

    public function sendEmailAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Reset password", $this->get("router")->generate("fos_user_resetting_request"));

        return parent::sendEmailAction($request);
    }

    public function checkEmailAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Reset password", $this->get("router")->generate("fos_user_resetting_request"));

        return parent::checkEmailAction($request);
    }

    public function resetAction(Request $request, $token)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Reset password", $this->get("router")->generate("fos_user_resetting_request"));

        return parent::resetAction($request, $token);
    }
}