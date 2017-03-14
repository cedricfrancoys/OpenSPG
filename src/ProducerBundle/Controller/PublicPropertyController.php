<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Properties', $this->get('router')->generate('producer_publicproperty_index'));

        $em = $this->getDoctrine()->getManager();

        $properties = $em->getRepository('ProducerBundle:Property')->findAll();

        $data = array(
            'properties' => $properties,
            'menu' => 'producer',
        );

        return $this->render('ProducerBundle:PublicProperty:index.html.twig', $data);
    }

    /**
     * @Route("/{id}")
     */
    public function showAction(Property $property)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Properties', $this->get('router')->generate('producer_publicproperty_index'));
        $breadcrumbs->addItem($property->getName(), $this->get('router')->generate('producer_publicproperty_show', array('id' => $property->getId())));

        $data = array(
            'property' => $property,
            'visits' => $property->getVisits(),
            'menu' => 'producer',
        );

        return $this->render('ProducerBundle:PublicProperty:show.html.twig', $data);
    }
}
