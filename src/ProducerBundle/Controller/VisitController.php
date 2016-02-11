<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ProducerBundle\Entity\Visit;
use ProducerBundle\Form\VisitType;

class VisitController extends Controller
{
    /**
     * @Route("/members/producer/visit/")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $visits = $em->getRepository('ProducerBundle:Visit')->findAll();

        return $this->render('ProducerBundle:Visit:index.html.twig', array(
            'visits' => $visits
        ));
    }

    /**
     * @Route("/members/producer/visit/add")
     */
    public function addAction(Request $request)
    {
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
     * @Route("/members/producer/visit/{id}")
     */
    public function editAction(Request $request)
    {
        $visit = new Visit();

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
}
