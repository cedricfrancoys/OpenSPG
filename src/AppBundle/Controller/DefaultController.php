<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nodes = $em->getRepository('NodeBundle:Node')->findAll();

        $data = array(
            'products' => array(),
            'nodes' => $nodes,
            'menu' => 'home'
        );

        return $this->render('AppBundle:Default:index.html.twig', $data);
    }
}
