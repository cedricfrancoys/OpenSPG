<?php

namespace ProductBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\Serializer\SerializerBuilder;
use ProductBundle\Entity\Product;
use ProductBundle\Form\ProductType;

/**
 * @Route("/producer/product/product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/get")
     */
    public function getAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $suggestion = $request->query->get('suggestion');

        $products = $em->
            getRepository('ProductBundle:Product')
            ->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        $response = new JsonResponse();
        $response->setData($products);

        return $response;
    }

    /**
     * @Route("/add")
     * @Method({"POST"})
     */
    public function addAction(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($product);
            $em->flush();

            $serializer = SerializerBuilder::create()->build();
            $json = $serializer->serialize($product, 'json');

            $response = new JsonResponse();
            $response->setContent($json);
            $response->setStatusCode(Response::HTTP_CREATED);

            return $response;
        }

        $errors = $form->getErrors(true);
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        $response = new JsonResponse();
        $response->setData($errors);
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);

        return $response;
    }
}
