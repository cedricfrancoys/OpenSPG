<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use UserBundle\Entity\User;
use ManagementBundle\Form\VisitGroupType;

/**
 * @Route("/grupo_visita")
 */
class VisitGroupController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
     * @Template
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Visit Group", $this->get("router")->generate("management_visitgroup_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

        $manager = $this->get('users.manager.user');
        $users = $manager->getUsersByRole(\UserBundle\Entity\User::ROLE_VISITGROUP);

        $data = array(
            'users' => $users,
            'menu' => 'management'
        );

        return $data;
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGER')")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Visit Group", $this->get("router")->generate("management_visitgroup_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_visitgroup_add"));

        $em = $this->getDoctrine()->getManager();

        $user = new User();

        $form = $this->createForm(VisitGroupType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('users.manager.user');
            $manager->setCurrentUser($this->getUser());
            $userCreated = $manager->createUser($user, $form, $request->request->get('manager'), array(\UserBundle\Entity\User::ROLE_MEMBER,\UserBundle\Entity\User::ROLE_VISITGROUP));
            if($userCreated)
            {
                $session = $this->get('session');
                $trans = $this->get('translator');

                // add flash messages
                $session->getFlashBag()->add(
                    'success',
                    $trans->trans('The visit group member has been updated!', array(), 'management')
                );

                if ($form->get('saveAndClose')->isClicked()) {
                    $url = $this->generateUrl('management_visitgroup_index');
                }else{
                    $url = $this->generateUrl('management_visitgroup_edit', array('id'=>$user->getId()));
                }
                $response = new RedirectResponse($url);

                return $response;
            }
        }

        return array(
            'form' => $form->createView(),
            'menu' => 'management'
        );
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGER')")
     * @Template()
     */
    public function editAction(user $user, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Visit Group", $this->get("router")->generate("management_visitgroup_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_visitgroup_edit",array('id'=>$user->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(VisitGroupType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_visitgroup_index');
            }else{
                $url = $this->generateUrl('management_visitgroup_edit', array('id'=>$user->getId()));
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
        $breadcrumbs->addItem("Visit Group", $this->get("router")->generate("management_visitgroup_index"));
        $breadcrumbs->addItem("Remove", $this->get("router")->generate("management_visitgroup_remove",array('id'=>$user->getId())));

        if (!$user) {
            throw $this->createNotFoundException('No user found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/visitgroup/remove')){
            $session->remove('confirmation/management/visitgroup/remove');

            if ($user->getNode() !== $this->getUser()->getNode()){
                throw new AccessDeniedException();
            }

            $user->removeRole(\UserBundle\Entity\User::ROLE_VISITGROUP);

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The user has been removed from the visit group!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_visitgroup_index'));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/visitgroup/remove', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key,
                'menu' => 'management'
            );
        }
    }
}
