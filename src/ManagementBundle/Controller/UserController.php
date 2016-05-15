<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use UserBundle\Entity\User;
use ManagementBundle\Form\UserType;

use ProducerBundle\Entity\Member as Producer;
use ConsumerBundle\Entity\Member as Consumer;

/**
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("User", $this->get("router")->generate("management_user_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $this->getUser();

        $users = $em->getRepository('UserBundle:User')->getAll($currentMember->getNode());

        $data = array(
            'users' => $users,
            'menu' => 'management'
        );

        return $data;
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function editAction(user $user, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Users", $this->get("router")->generate("management_user_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_user_edit",array('id'=>$user->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_user_index');
            }else{
                $url = $this->generateUrl('management_user_edit', array('id'=>$user->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return array(
            'form' => $form->createView(),
            'user' => $user,
            'menu' => 'management'
        );
    }

    /**
     * @Route("/{id}/remove")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function deleteAction(User $user, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Managers", $this->get("router")->generate("management_manager_index"));
        $breadcrumbs->addItem("Remove", $this->get("router")->generate("management_manager_remove",array('id'=>$user->getId())));

        if (!$user) {
            throw $this->createNotFoundException('No manager found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/user/delete')){
            $session->remove('confirmation/management/user/delete');

            if ($this->getUser()->getNode() !== $user->getNode()){
                throw new AccessDeniedException();
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The user has been deleted!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_user_index'));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/user/delete', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key,
                'menu' => 'management'
            );
        }
    }

    /**
     * @Route("/{id}/makeProducer")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function makeProducerAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $trans = $this->get('translator');

        $user->addRole('ROLE_PRODUCER');

        if( $user->getProducer() ){
            $producer = $user->getProducer();
        }else{
            $producer = new Producer();
            $producer->setActiveAsProducer(true);
            $em->persist($producer);
            $user->setProducer($producer);
        }

        $em->persist($user);

        $em->flush();

        // add flash messages
        $session->getFlashBag()->add(
            'success',
            $trans->trans('The user has been made a producer!', array(), 'producer')
        );

        return new RedirectResponse($this->generateUrl('management_producer_edit', array('id'=>$producer->getId())));
    }

    /**
     * @Route("/{id}/makeConsumer")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function makeConsumerAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $trans = $this->get('translator');

        $user->addRole('ROLE_CONSUMER');

        if( $user->getConsumer() ){
            $consumer = $user->getConsumer();
        }else{
            $consumer = new Consumer();
            $em->persist($consumer);
            $user->setConsumer($consumer);
        }

        $em->persist($user);

        $em->flush();

        // add flash messages
        $session->getFlashBag()->add(
            'success',
            $trans->trans('The user has been made a consumer!', array(), 'consumer')
        );

        return new RedirectResponse($this->generateUrl('management_consumer_edit', array('id'=>$consumer->getId())));
    }

    /**
     * @Route("/{id}/makeManager")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function makeManagerAction(User $user, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $this->get('session');
        $trans = $this->get('translator');

        $user->addRole('ROLE_MANAGEMENT');

        $em->persist($user);

        $em->flush();

        // add flash messages
        $session->getFlashBag()->add(
            'success',
            $trans->trans('The user has been made a manager!', array(), 'management')
        );

        return new RedirectResponse($this->generateUrl('management_manager_edit', array('id'=>$user->getId())));
    }
}
