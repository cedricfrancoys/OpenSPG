<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use ConsumerBundle\Entity\Member;
use ManagementBundle\Form\ConsumerType;
use UserBundle\Entity\User;

use UserBundle\Event\ConsumerEvent;

/**
 * @Route("/consumidor")
 */
class ConsumerController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("management_consumer_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

        $manager = $this->get('users.manager.user');
        $consumers = $manager->getUsersByRole(\UserBundle\Entity\User::ROLE_CONSUMER, 'Consumer', 'Producer');

        $data = array(
            'consumers' => $consumers,
            'menu' => 'management'
        );

        return $this->render('ManagementBundle:Consumer:index.html.twig', $data);
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("management_consumer_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_consumer_add"));

        $em = $this->getDoctrine()->getManager();

        $consumer = new Member();

        $form = $this->createForm(ConsumerType::class, $consumer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('users.manager.user');
            $manager->setCurrentUser($this->getUser());
            $userCreated = $manager->createUser($consumer->getUser(), $form, $request->request->get('consumer'), array(\UserBundle\Entity\User::ROLE_MEMBER, \UserBundle\Entity\User::ROLE_CONSUMER));
            if($userCreated)
            {
                $event = new ConsumerEvent($consumer);
                $dispatcher = $this->get('event_dispatcher'); 
                $dispatcher->dispatch('user.events.consumerCreated', $event);

                $session = $this->get('session');
                $trans = $this->get('translator');

                // add flash messages
                $session->getFlashBag()->add(
                    'success',
                    $trans->trans('The consumer has been created!', array(), 'management')
                );

                if ($form->get('saveAndClose')->isClicked()) {
                    $url = $this->generateUrl('management_consumer_index');
                }else{
                    $url = $this->generateUrl('management_consumer_edit', array('id'=>$consumer->getId()));
                }
                $response = new RedirectResponse($url);

                return $response;
            }
        }

        return $this->render('ManagementBundle:Consumer:add.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'management'
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGER')")
     * @Template
     */
    public function editAction(Member $consumer, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("management_consumer_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_consumer_edit",array('id'=>$consumer->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ConsumerType::class, $consumer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($consumer);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The consumers data has been updated!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                    $url = $this->generateUrl('management_consumer_index');
                }else{
                    $url = $this->generateUrl('management_consumer_edit', array('id'=>$consumer->getId()));
                }
            $response = new RedirectResponse($url);

            return $response;
        }

        return array(
            'form' => $form->createView(),
            'menu' => 'management'
        );
    }

    /**
     * @Route("/{id}/remove")
     * @Security("has_role('ROLE_MANAGER')")
     * @Template()
     */
    public function removeAction(User $user, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("management_consumer_index"));
        $breadcrumbs->addItem("Remove", $this->get("router")->generate("management_consumer_remove",array('id'=>$user->getId())));

        if (!$user) {
            throw $this->createNotFoundException('No consumer found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/consumer/remove')){
            $session->remove('confirmation/management/consumer/remove');

            if ($user->getNode() !== $this->getUser()->getNode()){
                throw new AccessDeniedException();
            }

            $user->removeRole(\UserBundle\Entity\User::ROLE_CONSUMER);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The user has been removed from consumer status!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_consumer_index'));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/consumer/remove', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key,
                'menu' => 'management'
            );
        }
    }
}
