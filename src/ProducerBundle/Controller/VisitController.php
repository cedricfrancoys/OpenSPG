<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ProducerBundle\Entity\Visit;
use ProducerBundle\Form\VisitType;

/**
 * @Route("/members/producer/visit")
 * @Security("has_role('ROLE_PRODUCER')")
 */
class VisitController extends Controller
{
    /**
     * @Route("/")
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
            ->where('p.User = :user OR m2.User = :user')
            ->setParameter('user', $this->getUser())
            ->getQuery()
            ->getResult();

        return $this->render('ProducerBundle:Visit:index.html.twig', array(
            'visits' => $visits
        ));
    }

    /**
     * @Route("/add")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visit_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("producer_visit_add"));

        $visit = new Visit();
        $visit->setVisitDate(new \DateTime());
        $visit->setStartTime(new \DateTime());
        $endTime = new \DateTime();
        $endTime->add(new \DateInterval('PT4H'));
        $visit->setEndTime($endTime);

        $form = $this->createForm(VisitType::class, $visit);
        // $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($visit);
            $em->flush();

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('producer_visit_edit');
                $response = new RedirectResponse($url);
            }

            return $response;
        }

        return $this->render('ProducerBundle:Visit:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}")
     */
    public function editAction(Request $request, $id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("producer_visit_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("producer_visit_edit", array('id'=>$id)));

        $visit = new Visit();

        $form = $this->createForm(VisitType::class, $visit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($visit);
            $em->flush();

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('producer_visit_edit');
                $response = new RedirectResponse($url);
            }

            return $response;
        }

        return $this->render('ProducerBundle:Visit:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
