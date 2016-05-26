<?php

namespace PageBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use PageBundle\Entity\Page;

class DefaultController extends Controller
{
    /**
     * @Route("/{path}")
     */
    public function indexAction(Page $page)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem($page->getTitle(), $this->get("router")->generate("page_default_index",array('path'=>$page->getPath())));

        return $this->render('PageBundle:Default:index.html.twig', array('page'=>$page));
    }
}
