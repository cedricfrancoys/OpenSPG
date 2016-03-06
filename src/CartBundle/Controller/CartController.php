<?php

namespace CartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use CartBundle\Entity\Cart;

/**
 * @Route("/cart")
 */
class CartController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Cart", $this->get("router")->generate("cart_cart_index"));

        return $this->render('CartBundle:Cart:index.html.twig');
    }

    /**
     * @Route("/add")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Cart", $this->get("router")->generate("cart_cart_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("cart_cart_add"));

        $em = $this->getDoctrine()->getManager();

        $product_id = $request->query->get('product_id');
        $member_id = $this->getUser();

        $this->addProductToCart($product_id, $member_id);

        $products = $em->
            getRepository('CartBundle:Cart')
            ->createQueryBuilder('c')
            ->select('c, s, p')
            ->leftJoin('c.Product', 's')
            ->leftJoin('s.Product', 'p')
            ->getQuery()
            ->getResult();

        $data = array(
            'products' => $products
        );

        return $this->render('CartBundle:Cart:cart.html.twig', $data);
    }

    /**
     * @Route("/rest")
     */
    public function restAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Cart", $this->get("router")->generate("cart_cart_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("cart_cart_add"));

        $em = $this->getDoctrine()->getManager();

        $product_id = $request->query->get('product_id');
        $member_id = $this->getUser();

        $this->restProductInCart($product_id, $member_id);

        $products = $em->
            getRepository('CartBundle:Cart')
            ->createQueryBuilder('c')
            ->select('c, s, p')
            ->leftJoin('c.Product', 's')
            ->leftJoin('s.Product', 'p')
            ->getQuery()
            ->getResult();

        $data = array(
            'products' => $products
        );

        return $this->render('CartBundle:Cart:cart.html.twig', $data);
    }

    /**
     * @Route("/update")
     * @Method({"POST"})
     */
    public function updateAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Cart", $this->get("router")->generate("cart_cart_index"));
        $breadcrumbs->addItem("Update", $this->get("router")->generate("cart_cart_update"));

        $em = $this->getDoctrine()->getManager();

        $member_id = $this->getUser();

        $amounts = $request->request->get('amount');
        foreach ($amounts as $stock_id => $amount) {
            $this->updateProductInCart($stock_id, $member_id, $amount); 
        }

        $products = $em->
            getRepository('CartBundle:Cart')
            ->createQueryBuilder('c')
            ->select('c, s, p')
            ->leftJoin('c.Product', 's')
            ->leftJoin('s.Product', 'p')
            ->getQuery()
            ->getResult();

        $data = array(
            'products' => $products
        );

        return $this->render('CartBundle:Cart:cart.html.twig', $data);
    }

    protected function updateProductInCart($product_id, $member_id, $amount)
    {
        $em = $this->getDoctrine()->getManager();

        $stock = $em->getRepository('ProducerBundle:Stock')->find($product_id);
        $member = $em->getRepository('MemberBundle:Member')->find($member_id);
        $cartItem = $em->getRepository('CartBundle:Cart')->findOneBy(array('Product'=>$stock, 'Member'=>$member));

        if( $cartItem ){
            $cartItem->setAmount($amount);
            $em->persist($cartItem);
            $em->flush();
        }
    }

    protected function addProductToCart($product_id, $member_id)
    {
        $em = $this->getDoctrine()->getManager();

        $stock = $em->getRepository('ProducerBundle:Stock')->find($product_id);
        $member = $em->getRepository('MemberBundle:Member')->find($member_id);
        $cartItem = $em->getRepository('CartBundle:Cart')->findOneBy(array('Product'=>$stock, 'Member'=>$member));

        if( $cartItem ){
            $cartItem->setAmount($cartItem->getAmount()+1);
        }else{
            $cartItem = new Cart();
            $cartItem->setProduct($stock);
            $cartItem->setMember($member);
            $cartItem->setAmount(1);
        }

        $em->persist($cartItem);
        $em->flush();
    }

    protected function restProductInCart($product_id, $member_id)
    {
        $em = $this->getDoctrine()->getManager();

        $stock = $em->getRepository('ProducerBundle:Stock')->find($product_id);
        $member = $em->getRepository('MemberBundle:Member')->find($member_id);
        $cartItem = $em->getRepository('CartBundle:Cart')->findOneBy(array('Product'=>$stock, 'Member'=>$member));

        if( $cartItem ){
            if ($cartItem->getAmount() == 1) {
                $em->remove($cartItem);
            }else{
                $cartItem->setAmount($cartItem->getAmount()-1);
                $em->persist($cartItem);
            }
            $em->flush();
        }

        
    }
}
