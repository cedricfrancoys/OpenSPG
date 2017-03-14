<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ProducerBundle\Entity\Member;

/**
 * @Route("/productores")
 */
class ProducerController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Producers', $this->get('router')->generate('producer_producer_index'));

        $em = $this->getDoctrine()->getManager();

        $producers = $em->getRepository('ProducerBundle:Member')->findAll();

        $data = array(
            'producers' => $producers,
            'menu' => 'producer',
        );

        return $this->render('ProducerBundle:Producer:index.html.twig', $data);
    }

    /**
     * @Route("/{id}")
     */
    public function showAction(Member $producer)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Producers', $this->get('router')->generate('producer_producer_index'));
        $breadcrumbs->addItem($producer->getUser()->getName(), $this->get('router')->generate('producer_producer_show', array('id' => $producer->getId())));

        $data = array(
            'producer' => $producer,
            'properties' => $producer->getProperties(),
        );

        return $this->render('ProducerBundle:Producer:show.html.twig', $data);
    }
}
