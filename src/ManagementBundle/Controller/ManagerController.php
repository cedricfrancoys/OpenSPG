<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use UserBundle\Entity\User;
use ManagementBundle\Form\ManagerType;

/**
 * @Route("/management/manager")
 */
class ManagerController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Manager", $this->get("router")->generate("management_manager_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->findOneBy(array('User'=>$this->getUser()));

        $managers = $em
            ->getRepository('UserBundle:User')
            ->createQueryBuilder('u')
            ->select('u')
            ->where('u.Node = :node')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('node', $currentMember->getNode())
            ->setParameter('role', 'ROLE_MANAGEMENT')
            ->getQuery()
            ->getResult();

        $data = array(
            'managers' => $managers
        );

        return $this->render('ManagementBundle:Manager:index.html.twig', $data);
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
        $breadcrumbs->addItem("Manager", $this->get("router")->generate("management_manager_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_manager_add"));

        $em = $this->getDoctrine()->getManager();

        $manager = new User();

        $form = $this->createForm(ManagerType::class, $manager);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($manager);
            $em->flush();

            $url = $this->generateUrl('management_manager_edit', array('id'=>$manager->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Manager:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
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

        return $this->render('ManagementBundle:Manager:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
