<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use NodeBundle\Entity\Node;
use ManagementBundle\Form\NodeType;

/**
 * @Route("/node")
 */
class NodeController extends Controller
{
    /**
     * @Route("/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function editAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Node", $this->get("router")->generate("management_node_edit"));

        $em = $this->getDoctrine()->getManager();

        $node_id = $this->getUser()->getNode()->getId();
        $node = $em->getRepository('NodeBundle:Node')
            ->createQueryBuilder('n')
            ->select('n,l')
            ->leftJoin('n.Location', 'l')
            ->where('n.id = :node')
            ->setParameter('node', $node_id)
            ->getQuery()
            ->getResult()
        ;
        $node = $node[0];

        $form = $this->createForm(NodeType::class, $node);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($node);
            $em->flush();

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_default_index');
            }else{
                $url = $this->generateUrl('management_node_edit', array('id'=>$node->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Node:edit.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'management'
        ));
    }
}
