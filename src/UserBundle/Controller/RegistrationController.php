<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use UserBundle\Entity\User;
use UserBundle\Form\RegistrationType;

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
        $breadcrumbs->addItem('Registration', $this->get('router')->generate('user_registration_register'));

        /** @var $formFactory FactoryInterface */
        // $formFactory = $this->get('fos_user.registration.form.factory');
        
        $userManager = $this->get('users.manager.user');

        $user = new User();
        $user->setEnabled(true);

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
                return $response;
            }
        }

        return array(
            'form' => $form->createView(),
        );
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
