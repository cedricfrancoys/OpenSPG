<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;

use NewsBundle\Entity\News;
use NewsBundle\Entity\Tags;
use ManagementBundle\Form\NewsType;

/**
 * @Route("/news")
 */
class NewsController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function indexAction()
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("News", $this->get("router")->generate("management_news_index"));

        $em = $this->getDoctrine()->getManager();

        $news = $em->getRepository('NewsBundle:News')->findAll();

        $data = array(
            'news' => $news,
            'menu' => 'management'
        );

        return $this->render('ManagementBundle:News:index.html.twig', $data);
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
        $breadcrumbs->addItem("News", $this->get("router")->generate("management_news_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_news_add"));

        $em = $this->getDoctrine()->getManager();

        $news = new News();

        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($news);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The news has been created!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_news_index');
            }else{
                $url = $this->generateUrl('management_news_edit', array('id'=>$news->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:News:add.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'management'
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGEMENT')")
     */
    public function editAction(News $news, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("News", $this->get("router")->generate("management_news_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_news_edit",array('id'=>$news->getId())));

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(NewsType::class, $news);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($news);
            $em->flush();

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('The news data has been updated!', array(), 'management')
            );

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_news_index');
            }else{
                $url = $this->generateUrl('management_news_edit', array('id'=>$news->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:News:edit.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'management'
        ));
    }
}
