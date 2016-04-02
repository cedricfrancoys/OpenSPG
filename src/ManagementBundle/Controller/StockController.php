<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use ProducerBundle\Entity\Stock;
use ManagementBundle\Form\StockType;

/**
 * @Route("/management/products")
 */
class StockController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("management_stock_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser());

        $sql = $em
            ->getRepository('ProducerBundle:Stock')
            ->createQueryBuilder('s')
            ->select('s,p,u')
            ->leftJoin('s.Producer', 'p')
            ->leftJoin('p.User', 'u')
            ->andWhere('u.Node = :node')
            ->setParameter('node', $currentMember->getNode());

        if($filter = $request->request->get('filter')){
            if ($filter['producer']) {
                $sql->andWhere('u.id = :user')
                    ->setParameter('user', $filter['producer']);
            }
        }

        $query = $sql->getQuery();
        $products = $query->getResult();

        $manager = $this->get('users.manager.user');
        $producers = $manager->getUsersByRole('ROLE_PRODUCER');

        $data = array(
            'products' => $products,
            'producers' => $producers
        );

        return $data;
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("management_stock_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_stock_add"));

        $em = $this->getDoctrine()->getManager();

        $stock = new Stock();

        $form = $this->createForm(StockType::class, $stock);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($stock);
            $em->flush();

            $url = $this->generateUrl('management_stock_edit', array('id'=>$stock->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Stock:add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function editAction(Stock $stock, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Products", $this->get("router")->generate("management_stock_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_stock_edit",array('id'=>$stock->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(StockType::class, $stock);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($stock);
            $em->flush();

            $url = $this->generateUrl('management_stock_edit', array('id'=>$stock->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Stock:edit.html.twig', array(
            'form' => $form->createView()
        ));
    }
}
