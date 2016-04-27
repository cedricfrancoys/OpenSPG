<?php

namespace ProducerBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ProducerBundle\Entity\Property;

/**
 * @Route("/propiedades")
 */
class PublicPropertyController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Properties", $this->get("router")->generate("producer_publicproperty_index"));

        $em = $this->getDoctrine()->getManager();

        $properties = $em->getRepository('ProducerBundle:Property')->findAll();

        $data = array(
            'properties' => $properties
        );

        return $this->render('ProducerBundle:PublicProperty:index.html.twig', $data);
    }

    /**
     * @Route("/{id}")
     */
    public function showAction(Property $property)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Properties", $this->get("router")->generate("producer_publicproperty_index"));
        $breadcrumbs->addItem($property->getName(), $this->get("router")->generate("producer_publicproperty_show",array('id'=>$property->getId())));

        $data = array(
            'property' => $property,
            'visits' => $property->getVisits()
        );

        return $this->render('ProducerBundle:PublicProperty:show.html.twig', $data);
    }
}
