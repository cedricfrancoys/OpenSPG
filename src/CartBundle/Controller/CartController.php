<?php

namespace CartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/cart")
 */
class CartController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('CartBundle:Default:index.html.twig');
    }

    /**
     * @Route("/add")
     */
    public function addAction()
    {
        return $this->render('CartBundle:Default:add.html.twig');
    }
}
