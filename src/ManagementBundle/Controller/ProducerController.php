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

use ProducerBundle\Entity\Member;
use ManagementBundle\Form\ProducerType;
use UserBundle\Entity\User;

/**
 * @Route("/producer")
 */
class ProducerController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));

        $currentMember = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $producers = $em->getRepository('UserBundle:User')->getUsersByRole('ROLE_PRODUCER', $currentMember->getNode(), 'Producer');

        $data = array(
            'producers' => $producers
        );

        return $this->render('ManagementBundle:Producer:index.html.twig', $data);
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_producer_add"));

        $em = $this->getDoctrine()->getManager();

        $producer = new Member();

        $form = $this->createForm(ProducerType::class, $producer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('users.manager.user');
            $manager->setCurrentUser($this->getUser());
            $userFormData = $request->request->get('producer');
            $userFormData = $userFormData['User'];
            $userCreated = $manager->createUser($producer->getUser(), $form, $userFormData, array('ROLE_MEMBER','ROLE_PRODUCER'));
            if($userCreated)
            {
                $session = $this->get('session');
                $trans = $this->get('translator');

                // add flash messages
                $session->getFlashBag()->add(
                    'success',
                    $trans->trans('The producer data has been updated!', array(), 'management')
                );

                if ($form->get('saveAndClose')->isClicked()) {
                    $url = $this->generateUrl('management_producer_index');
                }else{
                    $url = $this->generateUrl('management_producer_edit', array('id'=>$producer->getId()));
                }
                $response = new RedirectResponse($url);

                return $response;
            }
        }

        return $this->render('ManagementBundle:Producer:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function editAction(Member $producer, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_producer_edit",array('id'=>$producer->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ProducerType::class, $producer);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($producer);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The producer data has been updated!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_producer_index');
            }else{
                $url = $this->generateUrl('management_producer_edit', array('id'=>$producer->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Producer:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }



    /**
     * @Route("/{id}/remove")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function removeAction(User $user, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("management_producer_index"));
        $breadcrumbs->addItem("Remove", $this->get("router")->generate("management_producer_remove",array('id'=>$user->getId())));

        if (!$user) {
            throw $this->createNotFoundException('No producer found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/producer/remove')){
            $session->remove('confirmation/management/producer/remove');

            if ($user->getNode() !== $this->getUser()->getNode()){
                throw new AccessDeniedException();
            }

            $user->removeRole('ROLE_PRODUCER');

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The user has been removed from producer status!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_producer_index'));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/producer/remove', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key
            );
        }
    }
}
