<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistrationController extends Controller
{
    /**
     * @Route("/register")
     * @Template()
     */
    public function registerAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Registration', $this->get('router')->generate('fos_user_registration_register'));

        $response = parent::RegisterAction($request);

        $content = $response->getcontent();
        $registerLink = $this->generateUrl('fos_user_registration_register');
        $content = str_replace('<li><a href="'.$registerLink.'"', '<li class="active"><a href="'.$registerLink.'"', $content);
        $response->setContent($content);

        return $response;
    }

    /**
     * @Route("/register/checkUsername", options={"expose":true})
     * @Method({"POST"})
     */
    public function checkUsernameAction(Request $request)
    {
        $username = $request->request->get('username');

        if ($username) {
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository('UserBundle:User')->findBy(array('username' => $username));

            if (count($users)) {
                $response = new JsonResponse(array('usernameExists' => true));
            } else {
                $response = new JsonResponse(array('usernameExists' => false));
            }

            return $response;
        }

        throw new \Exception('Missing username');
    }
}
