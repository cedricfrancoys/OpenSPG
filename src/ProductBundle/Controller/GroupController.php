<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ProductBundle\Entity\ProductGroup;

class GroupController extends Controller
{
    /**
     * @Route("/product/group/get")
     */
    public function getAction(Request $request)
    {
        return $this->render('ProductBundle:Default:index.html.twig');
    }

    /**
     * @Route("/product/group/add")
     */
    public function addAction(Request $request)
    {
        $suggestion = $request->query->get('suggestion');

        $group = new ProductGroup();
        $group->setName($suggestion);

        $em = $this->getDoctrine()->getManager();

        $em->persist($group);
        $em->flush();

        $families = $em->getRepository('ProductBundle:Family')->findAll();

        $response = new JsonResponse();
        $response->setData($families);

        return $response;
    }
}
