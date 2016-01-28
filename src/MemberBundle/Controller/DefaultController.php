<?php

namespace MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/member/")
     */
    public function indexAction()
    {
        return $this->render('MemberBundle:Default:index.html.twig');
    }
}
