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

/**
 * @Route("/members/producer/property")
 */
class PropertyController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Properties", $this->get("router")->generate("producer_property_index"));

        $properties = $this->getUser()->getProducer()->getProperties();

        return $this->render('ProducerBundle:Property:index.html.twig', array(
            'properties' => $properties,
            'menu' => 'account'
        ));
    }

    /**
     * @Route("/add")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Properties", $this->get("router")->generate("producer_property_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("producer_property_add"));

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
            'form' => $form->createView(),
            'menu' => 'account'
        ));
    }

    /**
     * @Route("/{id}")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $property = $em->getRepository('ProducerBundle:Property')->find($id);

        if (!$property || $property->getMember()->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(PropertyType::class, $property);

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
