<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use UserBundle\Entity\User;

/**
 * @Route("/grupos")
 */
class GroupController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Groups', $this->get('router')->generate('app_group_index'));

        $em = $this->getDoctrine()->getManager();
        $nodes = $em->getRepository('NodeBundle:Node')->findAll();
        $managers = array();
        $visitors = array();
        foreach ($nodes as $node) {
            $managers[] = $em->getRepository('UserBundle:User')->getUsersByRole(User::ROLE_MANAGER, $node);
            $visitors[] = $em->getRepository('UserBundle:User')->getUsersByRole(User::ROLE_VISITGROUP, $node);
        }

        $data = array(
            'managers' => $managers,
            'visitors' => $visitors,
            'nodes' => $nodes,
        );

        return $this->render('AppBundle:Group:index.html.twig', $data);
    }

    /**
     * @Route("/{file}")
     */
    public function downloadAction($file)
    {
        $filePath = dirname(dirname(dirname(__DIR__))).'/web/downloads/'.$file;

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT);

        return $response;
    }
}
