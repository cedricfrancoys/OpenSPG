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

        $upcomingVisits = $em->getRepository('ProducerBundle:Visit')
            ->createQueryBuilder('v')
            ->where('v.visitDate IS NULL OR v.visitDate > :today')
            ->setParameter('today', new \DateTime())
            ->getQuery()
            ->getResult()
        ;

        $data = array(
            'products' => array(),
            'nodes' => $nodes,
            'menu' => 'home',
            'upcomingVisits' => $upcomingVisits
        );

        return $this->render('AppBundle:Default:index.html.twig', $data);
    }
}
