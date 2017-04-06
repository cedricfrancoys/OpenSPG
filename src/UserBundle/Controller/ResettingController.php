<?php

namespace UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use UserBundle\Form\ChangePasswordType;
use UserBundle\Entity\User;

class ResettingController extends Controller
{
    CONST RETRY_TTL = 86400;

    /**
     * @Route("/login/reset")
     * @Template()
     */
    public function requestAction()
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Reset password', $this->get('router')->generate('user_resetting_request'));

        return array();
    }

    /**
     * @Route("/login/reset/send_email")
     * @Template("UserBundle:Resetting:request.html.twig")
     */
    public function sendEmailAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Reset password', $this->get('router')->generate('user_resetting_sendemail'));
        $breadcrumbs->addItem('Sending email', $this->get('router')->generate('user_resetting_sendemail'));

        $em = $this->getDoctrine()->getManager();

        $userManager = $this->get('users.manager.user');

        $username = $request->request->get('username');
        /** @var $user UserInterface */
        $user = $userManager->findUserByUsernameOrEmail($username);
        
        $ttl = self::RETRY_TTL; // Time to live for the password request (24h?)
        if (null !== $user && !$user->isPasswordRequestNonExpired($ttl)) {
            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($user->generateToken());
            }
            
            $userManager->sendResettingEmailMessage($user);
            $user->setPasswordRequestedAt(new \DateTime());
            $em->persist($user);
            $em->flush();

            return new RedirectResponse($this->generateUrl('user_resetting_checkemail', array('username' => $username)));
        }

        if( null === $user ){
            return array('invalid_username'=> 'The provided username does not exist!');
        }elseif ($user->isPasswordRequestNonExpired($ttl)) {
            $passwordRequestedActivationDateTime = $user->getPasswordRequestedAt();
            $passwordRequestedActivationDateTime->add(new \DateInterval('PT'.(self::RETRY_TTL/3600).'H'));
            return array('in_ttl'=> 'You cannot reorder a password request yet!', 'restablish_password_request' => $passwordRequestedActivationDateTime->format('d-m-Y H:i'));
        }
    }

    /**
     * @Route("/login/reset/check_email")
     * @Template()
     */
    public function checkEmailAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Reset password', $this->get('router')->generate('user_resetting_request'));

        $username = $request->query->get('username');

        $userManager = $this->get('users.manager.user');
        $user = $userManager->findUserByUsernameOrEmail($username);

        return array('user'=>$user, 'tokenLifetime' => self::RETRY_TTL/3600);
    }

    /**
     * @Route("/login/reset/reset/{token}")
     * @Template()
     */
    public function resetAction(Request $request, $token)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Reset password', $this->get('router')->generate('user_resetting_request'));

        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('users.manager.user');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $encoder = $this->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($encoded);
            $user->setPasswordRequestedAt(null);
            $user->setConfirmationToken(null);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            if ($user->hasRole(User::ROLE_MANAGER)) {
                $url = $this->generateUrl('management_default_index');
            }elseif ($user->hasRole(User::ROLE_PRODUCER)) {
                $url = $this->generateUrl('producer_member_index');
            }else {
                $url = $this->generateUrl('consumer_member_profile');
            }
            return new RedirectResponse($url);
        }

        return array(
            'token' => $token,
            'form' => $form->createView(),
        );
    }
}
