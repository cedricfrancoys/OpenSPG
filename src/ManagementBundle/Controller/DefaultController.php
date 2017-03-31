<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class DefaultController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Management', $this->get('router')->generate('management_default_index'));

        $em = $this->getDoctrine()->getManager();

        $node = null;
        if ($this->getUser()) {
            $node = $this->getUser()->getNode();
        }

        $default_sort_order = array(
            'latest_user_registration' => 1,
            'latest_visits' => 2,
            'latest_properties' => 3,
            'fees' => 4
        );
        $sort_order = $default_sort_order;
        $user_attribs = $this->getUser()->getAttributes();
        $user_sort_order = $user_attribs['management']['dashboard']['sort_order'] ?: false;
        if ($user_sort_order) {
            $sort_order = array_merge($sort_order, $user_sort_order);
        }
        $numbered_sort_order = array();
        foreach ($sort_order as $k => $v) {
            $numbered_sort_order[$v] = $k;
        }
        ksort($numbered_sort_order);

        return $this->render('ManagementBundle:Default:index.html.twig', array(
            'menu' => 'management',
            'users' => $em->getRepository('UserBundle:User')->getLatest($node, 5),
            'visits' => $em->getRepository('ProducerBundle:Visit')->getLatestByNode(5, $node),
            'properties' => $em->getRepository('ProducerBundle:Property')->getLatestByNode(5, $node),
            'fees' => $em->getRepository('FeeBundle:Fee')->getPendingLatestByNode(5, $node),
            'sort_order' => $numbered_sort_order,
        ));
    }

    /**
     * @Route("/update_sort_order", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
     * @Method({"POST"})
     */
    public function updateSortOrderAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $sort_order = $request->request->get('sort_order');

        if (!is_array($sort_order)) {
            return new JsonResponse(array(
                'status' => 'ko',
                'msg' => 'The data sent must be an array but is not'
            ), 500);
        }

        $user = $this->getUser();
        $attribs = $user->getAttributes();
        $attribs['management']['dashboard']['sort_order'] = $sort_order;

        $user->setAttributes($attribs);
        $em->persist($user);
        $em->flush();

        return new JsonResponse(array(
            'status' => 'ok'
        ));
    }    
}
