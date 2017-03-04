<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
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

        $em = $this->getDoctrine()->getManager();

        $node = null;
        if ($this->getUser()) {
            $node = $this->getUser()->getNode();
        }

        return $this->render('ManagementBundle:Default:index.html.twig', array(
            'menu' => 'management',
            'users' => $em->getRepository('UserBundle:User')->getLatest($node, 5),
            'visits' => $em->getRepository('ProducerBundle:Visit')->getLatestByNode(5, $node),
            'properties' => $em->getRepository('ProducerBundle:Property')->getLatestByNode(5, $node)
        ));
    }
}
