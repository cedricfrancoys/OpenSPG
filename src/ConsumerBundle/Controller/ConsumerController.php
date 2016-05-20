<?php

namespace ConsumerBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use ConsumerBundle\Entity\Member;
use UserBundle\Entity\User;

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
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("consumer_consumer_index"));

        $em = $this->getDoctrine()->getManager();

        $consumers = $em->getRepository('ConsumerBundle:Member')->findAll();

        $data = array(
            'consumers' => $consumers,
            'menu' => 'consumer'
        );

        return $this->render('ConsumerBundle:Consumer:index.html.twig', $data);
    }

    /**
     * @Route("/{id}")
     */
    public function showAction(Member $consumer)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Consumers", $this->get("router")->generate("consumer_consumer_index"));
        $breadcrumbs->addItem($consumer->getUser()->getName(), $this->get("router")->generate("consumer_consumer_show",array('id'=>$consumer->getId())));

        $data = array(
            'consumer' => $consumer
        );

        return $this->render('ConsumerBundle:Consumer:show.html.twig', $data);
    }
}
