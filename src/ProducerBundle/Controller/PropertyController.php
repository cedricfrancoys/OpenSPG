<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use ProducerBundle\Entity\Property;
use ProducerBundle\Form\PropertyType;

class PropertyController extends Controller
{
    /**
     * @Route("/members/producer/property/")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $properties = $em->getRepository('ProducerBundle:Property')->findByUser($this->getUser());

        return $this->render('ProducerBundle:Property:index.html.twig', array(
            'properties' => $properties
        ));
    }

    /**
     * @Route("/members/producer/property/add")
     */
    public function addAction(Request $request)
    {
        $property = new Property();

        $form = $this->createForm(PropertyType::class, $property);
        // $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $member = $em->getRepository('ProducerBundle:Member')->getUser($this->getUser());
            $property->setMember($member);

            $em->persist($property);
            $em->flush();

            $url = $this->generateUrl('producer_property_edit', array('id'=>$property->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ProducerBundle:Property:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/members/producer/property/{id}")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $property = $em->getRepository('ProducerBundle:Property')->find($id);

        if (!$property || $property->getMember()->getMember()->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(PropertyType::class, $property);
        // $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($property);
            $em->flush();

            $url = $this->generateUrl('producer_property_edit');
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ProducerBundle:Property:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
