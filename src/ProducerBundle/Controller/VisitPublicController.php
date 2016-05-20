<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ProducerBundle\Entity\Visit;
use ProducerBundle\Form\VisitType;
use ProducerBundle\Form\SignMeUpType;
use ProducerBundle\Entity\Member;

/**
 * @Route("/visitas")
 */
class VisitPublicController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visitpublic_index"));

        $em = $this->getDoctrine()->getManager();

        $visits = $em->
            getRepository('ProducerBundle:Visit')
            ->createQueryBuilder('v')
            ->select('v')
            ->leftJoin('v.Producer', 'p')
            ->leftJoin('p.User', 'u')
            ->leftJoin('v.Property', 'pr')
            ->leftJoin('pr.Member', 'm2')
            ->leftJoin('m2.User', 'u2')
            ->getQuery()
            ->getResult();

        return $this->render('ProducerBundle:VisitPublic:index.html.twig', array(
            'visits' => $visits,
            'menu' => 'producer'
        ));
    }

    /**
     * @Route("/{id}")
     */
    public function showAction(Visit $visit, Request $request)
    {
        $producer = $visit->getProducer();

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visitpublic_index"));
        $breadcrumbs->addItem("Show", $this->get("router")->generate("producer_visitpublic_show", array('id'=>$visit->getId())));

        return $this->render('ProducerBundle:VisitPublic:show.html.twig', array(
            'visit' => $visit,
            'menu' => 'producer'
        ));
    }
}
