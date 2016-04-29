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
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("product_public_index"));

        $em = $this->getDoctrine()->getManager();

        $products = $em->
            getRepository('ProducerBundle:Stock')
            ->createQueryBuilder('s')
            ->select('s,pd,u')
            ->leftJoin('s.Product', 'p')
            ->leftJoin('s.Producer', 'pd')
            ->leftJoin('pd.User', 'u')
            ->getQuery()
            ->getResult();

        $data = array(
            'products' => $products,
            'menu' => 'product'
        );

        return $data;
    }
}
