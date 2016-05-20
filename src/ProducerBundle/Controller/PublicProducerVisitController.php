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
 * @Route("/productores/{id}/visitas")
 */
class PublicProducerVisitController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Member $producer)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_producer_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_publicproducervisit_index", array('id'=>$producer->getId())));

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
            ->where('m2 = :producer')
            ->setParameter('producer', $producer)
            ->getQuery()
            ->getResult();

        return $this->render('ProducerBundle:PublicProducerVisit:index.html.twig', array(
            'visits' => $visits,
            'menu' => 'producer'
        ));
    }

    /**
     * @Route("/{visit_id}")
     * @ParamConverter("visit", class="ProducerBundle:Visit", options={"mapping": {"visit_id": "id"}})
     */
    public function showAction(Visit $visit, Request $request)
    {
        $producer = $visit->getProducer();

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_producer_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_publicproducervisit_index", array('id'=>$producer->getId())));
        $breadcrumbs->addItem("Show", $this->get("router")->generate("producer_publicproducervisit_show", array('id'=>$visit->getProducer()->getId(),'visit_id'=>$visit->getId())));

        return $this->render('ProducerBundle:PublicProducerVisit:show.html.twig', array(
            'visit' => $visit,
            'menu' => 'producer'
        ));
    }
}
