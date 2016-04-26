<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use Symfony\Cmf\Bundle\RoutingBundle\Doctrine\Phpcr\Route as Orm_Route;

use PageBundle\Entity\Page;
use ManagementBundle\Form\PageType;

/**
 * @Route("/pages")
 */
class PageController extends Controller
{
    /**
     * @Route("/")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Page", $this->get("router")->generate("management_page_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser()->getId());

        $pages = $em
            ->getRepository('PageBundle:Page')
            ->createQueryBuilder('p')
            ->select('p')
            ->getQuery()
            ->getResult();

        $data = array(
            'pages' => $pages
        );

        return $data;
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Page", $this->get("router")->generate("management_page_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_page_add"));

        $em = $this->getDoctrine()->getManager();

        $page = new Page();

        $form = $this->createForm(PageType::class, $page);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $page->setCreatedAt(new \DateTime());
            $page->setCreatedBy($this->getUser());
            $page->setUpdatedAt(new \DateTime());
            $page->setUpdatedBy($this->getUser());
            $em->persist($page);

            $route = new Orm_Route();
            $route->setName($page->getName());
            $route->setDefault('_controller', 'PageBundle:Default:index');
            $route->setRouteContent($page);
            $route->setVariablePattern($page->getPath());
            $em->persist($route);

            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The page has been added!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_page_index');
            }else{
                $url = $this->generateUrl('management_page_edit', array('id'=>$page->getId()));
            }
            return new RedirectResponse($url);
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function editAction(Page $page, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Page", $this->get("router")->generate("management_page_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_page_edit",array('id'=>$page->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(PageType::class, $page);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $page->setUpdatedAt(new \DateTime());
            $page->setUpdatedBy($this->getUser());
            $em->persist($page);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The page has been updated!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_page_index');
            }else{
                $url = $this->generateUrl('management_page_edit', array('id'=>$page->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{id}/markPaid")
     * @Security("has_role('ROLE_MANAGEMENT')")
     * @Template()
     */
    public function markPaidAction(Fee $fee, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Fees", $this->get("router")->generate("management_fee_index"));
        $breadcrumbs->addItem("Mark as paid", $this->get("router")->generate("management_fee_markpaid",array('id'=>$fee->getId())));

        if (!$fee) {
            throw $this->createNotFoundException('Fee not found');
        }

        $session = $this->get('session');
        $trans = $this->get('translator');

        if($request->request->get('confirmation_key') && $request->request->get('confirmation_key') == $session->get('confirmation/management/fee/markPaid')){
            $session->remove('confirmation/management/fee/markPaid');

            $user = $fee->getUser();
            if ($user->getNode() !== $this->getUser()->getNode()){
                throw new AccessDeniedException();
            }

            $fee->setStatus(Fee::STATUS_PAID);

            $em = $this->getDoctrine()->getManager();
            $em->persist($fee);
            $em->flush();

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The fee has been marked as paid!', array(), 'management')
            );

            return new RedirectResponse($this->generateUrl('management_fee_index'));
        }else{
            $confirmation_key = uniqid();
            $session->set('confirmation/management/fee/markPaid', $confirmation_key);

            return array(
                'confirmation_key' => $confirmation_key
            );
        }
    }
}
