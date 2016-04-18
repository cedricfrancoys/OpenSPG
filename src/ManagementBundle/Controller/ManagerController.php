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
use ManagementBundle\Form\ManagerType;

/**
 * @Route("/manager")
 */
class ManagerController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Manager", $this->get("router")->generate("management_manager_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

        $manager = $this->get('users.manager.user');
        $managers = $manager->getUsersByRole('ROLE_MANAGEMENT');

        $data = array(
            'managers' => $managers
        );

        return $data;
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Manager", $this->get("router")->generate("management_manager_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_manager_add"));

        $em = $this->getDoctrine()->getManager();

        $user = new User();

        $form = $this->createForm(ManagerType::class, $user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $manager = $this->get('users.manager.user');
            $manager->setCurrentUser($this->getUser());
            $userCreated = $manager->createUser($user, $form, $request->request->get('manager'), array('ROLE_MEMBER','ROLE_MANAGEMENT'));
            if($userCreated)
            {
                $session = $this->get('session');
                $trans = $this->get('translator');

                // add flash messages
                $session->getFlashBag()->add(
                    'success',
                    $trans->trans('The managers data has been updated!', array(), 'management')
                );

                $url = $this->generateUrl('management_manager_edit', array('id'=>$user->getId()));
                $response = new RedirectResponse($url);

                return $response;
            }
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function editAction(user $manager, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Manager", $this->get("router")->generate("management_manager_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_manager_edit",array('id'=>$manager->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(ManagerType::class, $manager);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($manager);
            $em->flush();

            $url = $this->generateUrl('management_manager_edit', array('id'=>$manager->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return array(
            'form' => $form->createView()
        );
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
        $breadcrumbs->addItem("Managers", $this->get("router")->generate("management_manager_index"));
        $breadcrumbs->addItem("Remove", $this->get("router")->generate("management_manager_remove",array('id'=>$user->getId())));

        if (!$user) {
            throw $this->createNotFoundException('No manager found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/manager/remove')){
            $session->remove('confirmation/management/manager/remove');

            if ($user->getNode() !== $this->getUser()->getNode()){
                throw new AccessDeniedException();
            }

            $user->removeRole('ROLE_MANAGEMENT');

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The user has been removed from manager status!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_manager_index'));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/manager/remove', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key
            );
        }
    }
}
