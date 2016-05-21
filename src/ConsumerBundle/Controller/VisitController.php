<?php

namespace ConsumerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ProducerBundle\Entity\Visit;

use ConsumerBundle\Form\VisitType;

use ProducerBundle\Event\VisitEvent;

/**
 * @Route("/consumidor/visitas")
 */
class VisitController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_CONSUMER')")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("consumer_member_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("consumer_visit_index"));

        $em = $this->getDoctrine()->getManager();
        $visits = $em->getRepository('ProducerBundle:Visit')
            ->createQueryBuilder('v')
            ->leftJoin('v.Participants', 'p')
            ->where('p = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()
            ->getResult();

        $data = array(
            'menu' => 'account',
            'visits' => $visits
        );

        return $this->render('ConsumerBundle:Visit:index.html.twig', $data);
    }

    /**
     * @Route("/{id}")
     * @Security("has_role('ROLE_CONSUMER')")
     */
    public function showAction(Request $request, Visit $visit){

    	$breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("consumer_member_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("consumer_visit_index"));
        $breadcrumbs->addItem("Show", $this->get("router")->generate("consumer_visit_show",array('id'=>$visit->getId())));

        return $this->render('ConsumerBundle:Visit:show.html.twig', array(
    		'visit' => $visit,
            'menu' => 'account'
    	));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_CONSUMER')")
     */
    public function editAction(Request $request, Visit $visit){

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("consumer_member_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("consumer_visit_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("consumer_visit_edit",array('id'=>$visit->getId())));

        $visitAccepted = $visit->getAccepted();

        $form = $this->createForm(VisitType::class, $visit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($visit);
            $em->flush();

            if ($visit->getAccepted() !== null && $visitAccepted === null) {
                $event = new VisitEvent($visit, 'edit');
                $dispatcher = $this->get('event_dispatcher');
                $dispatcher->dispatch('producer.events.visitCompleted', $event);
            }

            $url = $this->generateUrl('consumer_visit_edit');
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ConsumerBundle:Visit:edit.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'account'
        ));
    }
}
