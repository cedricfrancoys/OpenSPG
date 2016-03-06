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

use ProducerBundle\Entity\Member;
use ProducerBundle\Form\MemberType;
use ProducerBundle\Form\ProfileType;
use ProducerBundle\Form\RegistrationType;
use UserBundle\Entity\User;

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
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producers", $this->get("router")->generate("producer_producer_index"));

        $em = $this->getDoctrine()->getManager();

        $producers = $em->getRepository('ProducerBundle:Member')->findAll();

        $data = array(
            'producers' => $producers
        );

        return $this->render('ProducerBundle:Producer:index.html.twig', $data);
    }

    /**
     * @Route("/{id}")
     */
    public function showAction($id)
    {
        return $this->render('ProducerBundle:Producer:index.html.twig');
    }
}
