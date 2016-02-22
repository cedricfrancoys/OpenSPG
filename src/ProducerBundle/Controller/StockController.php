<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use ProducerBundle\Entity\Stock;
use ProductBundle\Entity\Product;
use ProducerBundle\Form\StockType;
use ProductBundle\Form\ProductType;

class StockController extends Controller
{
    /**
     * @Route("/members/producer/product/")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('ProducerBundle:Stock')->findByUser($this->getUser());

        return $this->render('ProducerBundle:Stock:index.html.twig', array(
            'products' => $products
        ));
    }

    /**
     * @Route("/members/producer/product/add")
     */
    public function addAction(Request $request)
    {
        $stock = new Stock();
        $product = new Product();

        $form = $this->createForm(StockType::class, $stock);
        $pForm = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {
            $member = $em->getRepository('ProducerBundle:Member')->getUser($this->getUser());
            $stock->setProducer($member);

            $em->persist($stock);
            $em->flush();

            $url = $this->generateUrl('producer_stock_edit', array('id'=>$stock->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        $groups = $em->getRepository('ProductBundle:ProductGroup')->findAll();
        $families = $em->getRepository('ProductBundle:Family')->findAll();
        $varieties = $em->getRepository('ProductBundle:Variety')->findAll();

        return $this->render('ProducerBundle:Stock:edit.html.twig', array(
            'form' => $form->createView(),
            'pForm' => $pForm->createView(),
            'groups' => json_encode($groups),
            'families' => json_encode($families),
            'varieties' => json_encode($varieties),
            'family_get_path' => $this->generateUrl('product_family_get'),
            'group_add_path' => $this->generateUrl('product_group_add')
        ));
    }

    /**
     * @Route("/members/producer/property/{id}")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $property = $em->getRepository('ProducerBundle:Property')->find($id);

        if (!$property || $property->getMember()->getMember()->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(PropertyType::class, $property);
        // $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($property);
            $em->flush();

            $url = $this->generateUrl('producer_property_edit');
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ProducerBundle:Property:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
