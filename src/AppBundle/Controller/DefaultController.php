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
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;

        $stocks = $em->getRepository('ProducerBundle:Stock')
            ->createQueryBuilder('s')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;

        $news = $em->getRepository('NewsBundle:News')
            ->createQueryBuilder('n')
            ->setMaxResults(3)
            ->getQuery()
            ->getResult()
        ;        

        $data = array(
            'products' => $stocks,
            'nodes' => $nodes,
            'menu' => 'home',
            'upcomingVisits' => $upcomingVisits,
            'news' => $news
        );

        return $this->render('AppBundle:Default:index.html.twig', $data);
    }

    /**
     * @Route("/legal")
     */
    public function legalAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Legal", $this->get("router")->generate("app_default_legal"));

        $data = array(
            'menu' => 'legal'
        );

        return $this->render('AppBundle:Default:legal.html.twig', $data);
    }

    /**
     * @Route("/politica_de_cookies")
     */
    public function cookiesAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Cookie Politic", $this->get("router")->generate("app_default_cookies"));

        $data = array(
            'menu' => 'cookies'
        );

        return $this->render('AppBundle:Default:cookies.html.twig', $data);
    }

    /**
     * @Route("/el_spg")
     */
    public function aboutAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("The PGS", $this->get("router")->generate("app_default_about"));

        $data = array(
            'menu' => 'about'
        );

        return $this->render('AppBundle:Default:about.html.twig', $data);
    }
}
