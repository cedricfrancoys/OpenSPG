<?php

namespace ConsumerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use ConsumerBundle\Entity\Member;

/**
 * @Route("/consumidores")
 */
class ConsumerController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Consumers', $this->get('router')->generate('consumer_consumer_index'));

        $em = $this->getDoctrine()->getManager();

        $consumers = $em->getRepository('ConsumerBundle:Member')->findAll();

        $data = array(
            'consumers' => $consumers,
            'menu' => 'consumer',
        );

        return $this->render('ConsumerBundle:Consumer:index.html.twig', $data);
    }

    /**
     * @Route("/{id}")
     */
    public function showAction(Member $consumer)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Consumers', $this->get('router')->generate('consumer_consumer_index'));
        $breadcrumbs->addItem($consumer->getUser()->getName(), $this->get('router')->generate('consumer_consumer_show', array('id' => $consumer->getId())));

        $data = array(
            'consumer' => $consumer,
        );

        return $this->render('ConsumerBundle:Consumer:show.html.twig', $data);
    }
}
