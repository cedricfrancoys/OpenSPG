<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class MemberController extends Controller
{
    /**
     * @Route("/members/producer/")
     */
    public function indexAction()
    {
        return $this->render('ProducerBundle:Member:index.html.twig');
    }
}
