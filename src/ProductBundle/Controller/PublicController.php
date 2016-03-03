<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\Serializer\SerializerBuilder;

use ProductBundle\Entity\Product;
use ProductBundle\Form\ProductType;

/**
* @Route("/productos")
*/
class PublicController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $products = $em->
            getRepository('ProducerBundle:Stock')
            ->createQueryBuilder('s')
            ->select('s')
            ->leftJoin('s.Product', 'p')
            ->leftJoin('s.Producer', 'pd')
            ->leftJoin('pd.Member', 'm')
            ->getQuery()
            ->getResult();

        $data = array(
            'products' => $products
        );

        return $data;
    }
}
