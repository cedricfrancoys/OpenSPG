<?php

namespace PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * 
     */
    public function indexAction($contentDocument)
    {
        return $this->render('PageBundle:Default:index.html.twig');
    }
}
