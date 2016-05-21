<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ProducerBundle\Entity\Visit;
use ProducerBundle\Form\VisitType;
use ProducerBundle\Form\SignMeUpType;

/**
 * @Route("/productor/visita")
 */
class VisitController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visit_index"));

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
            ->where('u = :user OR u2 = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()
            ->getResult();

        return $this->render('ProducerBundle:Visit:index.html.twig', array(
            'visits' => $visits,
            'menu' => 'account'
        ));
    }

    /**
     * @Route("/{id}")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function showAction(Request $request, Visit $visit)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My Account", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visit_index"));
        $breadcrumbs->addItem("Show", $this->get("router")->generate("producer_visit_show", array('id'=>$visit->getId())));

        return $this->render('ProducerBundle:Visit:show.html.twig', array(
            'visit' => $visit,
            'menu' => 'account'
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function editAction(Request $request, Visit $visit){

        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visit_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("producer_visit_edit",array('id'=>$visit->getId())));

        if (!$this->checkEditAllowed($visit)) {
            throw new AccessDeniedException();
        }

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

            $url = $this->generateUrl('producer_visit_edit', array('id'=>$visit->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ProducerBundle:Visit:edit.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'account'
        ));
    }

    protected function checkEditAllowed(Visit $visit)
    {
        if ($visit->getProducer()->getUser() === $this->getUser()) {
            return true;
        }

        $participants = $visit->getParticipants();
        
        foreach ($participants as $participant) {
            if ($participant === $this->getUser()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @Route("/{id}/signMeUp")
     */
    public function signMeUpAction(Visit $visit, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visitpublic_index"));
        $breadcrumbs->addItem("Sign me up", $this->get("router")->generate("producer_visit_signmeup", array('id'=>$visit->getId())));

        $form = $this->createForm(SignMeUpType::class, null);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $visit->addParticipant($this->getUser());

            $em->persist($visit);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('You have been added to the visit!', array(), 'visit')
            );

            $url = $this->generateUrl('producer_visitpublic_show', array('id'=>$visit->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ProducerBundle:Visit:signmeup.html.twig', array(
            'form' => $form->createView(),
            'visit' => $visit,
            'menu' => 'producer'
        ));
    }
}
