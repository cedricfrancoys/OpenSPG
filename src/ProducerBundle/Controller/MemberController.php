<?php

namespace ProducerBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ProducerBundle\Entity\Member;
use ProducerBundle\Form\MemberType;
use ProducerBundle\Form\ProfileType;
use ProducerBundle\Form\RegistrationType;
use UserBundle\Entity\User;

class MemberController extends Controller
{
    /**
     * @Route("/members/producer/")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function indexAction()
    {
        return $this->render('ProducerBundle:Member:index.html.twig');
    }

    /**
     * @Route("/members/producer/register/")
     */
    public function registerAction(Request $request)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $member = new Member();

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            return $event->getResponse();
        }

        $form = $this->createForm(RegistrationType::class, $member);
        // $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            $pUser = $request->request->get('producerRegistration');
            $pUser = $pUser['Member']['User'];
            $user->setEmail($pUser['email']);
            $user->setPlainPassword($pUser['password']['first']);
            $user->setUsername($pUser['username']);
            
            $user->addRole('ROLE_MEMBER');
            $user->addRole('ROLE_CONSUMER');
            $user->addRole('ROLE_PRODUCER');
            $userManager->updateUser($user);

            $em = $this->getDoctrine()->getManager();

            $member->getMember()->setUser($user);

            $em->persist($member);
            $em->flush();

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('producer_member_profile');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('ProducerBundle:Member:register.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/members/producer/profile/")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function profileAction(Request $request){

    	$em = $this->getDoctrine()->getManager();

        $member = $em->getRepository('ProducerBundle:Member')->getUser($this->getUser());

    	$form = $this->createForm(ProfileType::class, $member);

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
            
             /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
            $userManager = $this->get('fos_user.user_manager');
            $user = $userManager->findUserBy(array('id'=>$this->getUser()->getId()));

            $pUser = $request->request->get('profile');
            $pUser = $pUser['Member']['User'];
            $user->setEmail($pUser['email']);
            $user->setUsername($pUser['username']);
            
            $userManager->updateUser($user);

            $em->persist($member);
		    $em->flush();

		    return $this->redirectToRoute('producer_member_profile');
		}


    	return $this->render('ProducerBundle:Member:profile.html.twig', array(
    		'form' => $form->createView()
    	));
    }
}
