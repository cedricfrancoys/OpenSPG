<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use UserBundle\Entity\User;
use ConsumerBundle\Entity\Member as Consumer;
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

        $userManager = $this->get('users.manager.user');

        $user = new User();
        $user->setEnabled(true);

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                // Set initial roles for the new user
                $user->addRole(User::ROLE_MEMBER);
                $user->addRole(User::ROLE_CONSUMER);

                $userManager->updateCanonicalFields($user);
                $encoder = $this->get('security.password_encoder');
                $userManager->hashPassword($user, $encoder);

                $consumer = new Consumer();
                $user->setConsumer($consumer);

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $url = $this->generateUrl('user_registration_confirmed');
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

    /**
     * @Route("/register/confirmed")
     * @Template()
     */
    public function confirmedAction(Request $request)
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession(),
        );
    }

    /**
     * @return mixed
     */
    private function getTargetUrlFromSession()
    {
        $key = sprintf('_security.%s.target_path', $this->get('security.token_storage')->getToken()->getProviderKey());

        if ($this->get('session')->has($key)) {
            return $this->get('session')->get($key);
        }
    }
}
