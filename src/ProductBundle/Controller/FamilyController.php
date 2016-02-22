<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ProductBundle\Entity\Family;

class FamilyController extends Controller
{
    /**
     * @Route("/product/family/get")
     */
    public function getAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $families = $em->getRepository('ProductBundle:Family')->findAll();

        $response = new JsonResponse();
        $response->setData($families);

        return $response;
    }

    /**
     * @Route("/product/family/add")
     */
    public function addAction(Request $request)
    {
        $suggestion = $request->query->get('suggestion');

        $family = new Family();
        $family->setName($suggestion);

        $em = $this->getDoctrine()->getManager();

        $em->persist($family);
        $em->flush();

        
    }
}
