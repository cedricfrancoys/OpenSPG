<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use ProducerBundle\Entity\Stock;
use ProductBundle\Entity\Product;
use ProducerBundle\Form\StockType;
use ProductBundle\Form\ProductType;

/**
 * @Route("/members/producer/product")
 */
class StockController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("producer_stock_index"));

        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('ProducerBundle:Stock')->findByUser($this->getUser());

        return $this->render('ProducerBundle:Stock:index.html.twig', array(
            'products' => $products
        ));
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_PRODUCER')")
     * @Method({"POST"})
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("producer_stock_index"));
        $breadcrumbs->addItem("add", $this->get("router")->generate("producer_stock_add"));

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

        $groups = $em->
            getRepository('ProductBundle:ProductGroup')
            ->createQueryBuilder('g')
            ->select('g')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $families = array();
        $varieties = array();

        return $this->render('ProducerBundle:Stock:edit.html.twig', array(
            'form' => $form->createView(),
            'pForm' => $pForm->createView(),
            'groups' => json_encode($groups),
            'families' => json_encode($families),
            'varieties' => json_encode($varieties),
            'group_get_path' => $this->generateUrl('product_group_get'),
            'group_add_path' => $this->generateUrl('product_group_add'),
            'family_get_path' => $this->generateUrl('product_family_get'),
            'family_add_path' => $this->generateUrl('product_family_add'),
            'variety_get_path' => $this->generateUrl('product_variety_get'),
            'variety_add_path' => $this->generateUrl('product_variety_add'),
            'product_add_path' => $this->generateUrl('product_product_add')
        ));
    }

    /**
     * @Route("/edit/{id}")
     * @Security("has_role('ROLE_PRODUCER')")
     */
    public function editAction(Request $request, $id)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Producer", $this->get("router")->generate("producer_member_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("producer_stock_index"));
        $breadcrumbs->addItem("edit", $this->get("router")->generate("producer_stock_edit", array('id'=>$id)));

        $em = $this->getDoctrine()->getManager();
        $stock = $em->getRepository('ProducerBundle:Stock')->find($id);
        $product = new Product();

        if (!$stock || $stock->getProducer()->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(StockType::class, $stock);
        $pForm = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($stock);
            $em->flush();

            $url = $this->generateUrl('producer_stock_edit');
            $response = new RedirectResponse($url);

            return $response;
        }

        $groups = $em->
            getRepository('ProductBundle:ProductGroup')
            ->createQueryBuilder('g')
            ->select('g')
            ->getQuery()
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
        $families = array();
        $varieties = array();

        return $this->render('ProducerBundle:Stock:edit.html.twig', array(
            'form' => $form->createView(),
            'pForm' => $pForm->createView(),
            'groups' => json_encode($groups),
            'families' => json_encode($families),
            'varieties' => json_encode($varieties),
            'group_get_path' => $this->generateUrl('product_group_get'),
            'group_add_path' => $this->generateUrl('product_group_add'),
            'family_get_path' => $this->generateUrl('product_family_get'),
            'family_add_path' => $this->generateUrl('product_family_add'),
            'variety_get_path' => $this->generateUrl('product_variety_get'),
            'variety_add_path' => $this->generateUrl('product_variety_add'),
            'product_add_path' => $this->generateUrl('product_product_add')
        ));
    }
}
