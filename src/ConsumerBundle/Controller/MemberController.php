<?php

namespace ConsumerBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ConsumerBundle\Entity\Member;
use ConsumerBundle\Form\MemberType;
use ConsumerBundle\Form\ProfileType;
use ConsumerBundle\Form\RegistrationType;
use UserBundle\Entity\User;

use UserBundle\Event\ConsumerEvent;

/**
 * @Route("/consumidor")
 */
class MemberController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_CONSUMER')")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("consumer_member_index"));

        $data = array(
            'menu' => 'account'
        );

        return $this->render('ConsumerBundle:Member:index.html.twig', $data);
    }

    /**
     * @Route("/registro")
     */
    public function registerAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Registration", $this->get("router")->generate("fos_user_registration_register"));
        $breadcrumbs->addItem("Consumer registration", $this->get("router")->generate("consumer_member_register"));

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

            $pUser = $request->request->get('consumerRegistration');
            $pUser = $pUser['User'];
            $user->setEmail($pUser['email']);
            $user->setPlainPassword($pUser['password']['first']);
            $user->setUsername($pUser['username']);
            $user->setName($pUser['name']);
            $user->setSurname($pUser['surname']);
            $user->setPhone($pUser['phone']);
            
            $user->addRole('ROLE_MEMBER');
            $user->addRole('ROLE_CONSUMER');
            $userManager->updateUser($user);

            $em = $this->getDoctrine()->getManager();

            $member->setUser($user);

            $em->persist($member);
            $em->flush();

            $consumerEvent = new ConsumerEvent($consumer);
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch('user.events.consumerCreated', $consumerEvent);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('consumer_member_profile');
                $response = new RedirectResponse($url);
            }

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            return $response;
        }

        return $this->render('ConsumerBundle:Member:register.html.twig', array(
            'form' => $form->createView(),
            'menu'=> 'register'
        ));
    }

    /**
     * @Route("/perfil")
     * @Security("has_role('ROLE_CONSUMER')")
     */
    public function profileAction(Request $request){

    	$breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("consumer_member_index"));
        $breadcrumbs->addItem("Profile", $this->get("router")->generate("consumer_member_profile"));

        $em = $this->getDoctrine()->getManager();

        $member = $em->getRepository('ConsumerBundle:Member')->getUser($this->getUser());

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

		    return $this->redirectToRoute('consumer_member_profile');
		}


    	return $this->render('ConsumerBundle:Member:profile.html.twig', array(
    		'form' => $form->createView(),
            'menu' => 'account'
    	));
    }
}
