<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ProductBundle\Entity\ProductGroup;

/**
* @Route("/producer/product/group")
*/
class GroupController extends Controller
{
    /**
     * @Route("/get")
     */
    public function getAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $groups = $em->
        	getRepository('ProductBundle:ProductGroup')
        	->createQueryBuilder('g')
        	->select('e')
        	->getQuery()
        	->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new JsonResponse();
        $response->setData($groups);

        return $response;
    }

    /**
     * @Route("/add")
     * @Method({"POST"})
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $suggestion = $request->request->get('suggestion');

        if(!$em->getRepository('ProductBundle:ProductGroup')->findOneByName($suggestion))
        {
        	$group = new ProductGroup();
	        $group->setName($suggestion);

	        $em->persist($group);
	        $em->flush();
        }

        $groups = $em->
        	getRepository('ProductBundle:ProductGroup')
        	->createQueryBuilder('g')
        	->select('g')
        	->getQuery()
        	->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new JsonResponse();
        $response->setData($groups);

        return $response;
    }
}
