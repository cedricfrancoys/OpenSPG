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
        return $this->render('ProducerBundle:Producer:index.html.twig');
    }

    /**
     * @Route("/{id}")
     */
    public function showAction($id)
    {
        return $this->render('ProducerBundle:Producer:index.html.twig');
    }
}
