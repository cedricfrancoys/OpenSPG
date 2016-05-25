<?php

namespace ManagementBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use ProducerBundle\Entity\Visit;
use ManagementBundle\Form\VisitType;
use ProducerBundle\Entity\Member;

use ProducerBundle\Event\VisitEvent;

/**
 * @Route("/visitas")
 */
class VisitController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function indexAction(Request $request)
    {
        
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("management_visit_index"));

        $em = $this->getDoctrine()->getManager();

        $currentMember = $em->getRepository('UserBundle:User')->find($this->getUser());

        $visits = $em->getRepository('ProducerBundle:Visit')->getFiltered($currentMember, (array)$request->get('filter'));

        $manager = $this->get('users.manager.user');
        $producers = $manager->getUsersByRole(\UserBundle\Entity\User::ROLE_PRODUCER);
        // @ToDo Refactor into Repository
        $properties = $em->getRepository('ProducerBundle:Property')->getByNode($currentMember->getNode());

        $data = array(
            'visits' => $visits,
            'producers' => $producers,
            'properties' => $properties,
            'menu' => 'management'
        );

        return $this->render('ManagementBundle:Visit:index.html.twig', $data);
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("management_visit_index"));
        $breadcrumbs->addItem("Add", $this->get("router")->generate("management_visit_add"));

        $em = $this->getDoctrine()->getManager();

        $visit = new Visit();

        $form = $this->createForm(VisitType::class, $visit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($visit);

            if ($visit->getDocumentFile()) {
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $uploadableManager->markEntityToUpload($visit, $visit->getDocumentFile());
            }

            $em->flush();

            $event = new VisitEvent($visit, 'add');
            $dispatcher = $this->get('event_dispatcher'); 
            $dispatcher->dispatch('producer.events.visitPersisted', $event);
            if ($visit->getAccepted() !== null) {
                $dispatcher->dispatch('producer.events.visitCompleted', $event);
            }

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_visit_index');
            }else{
                $url = $this->generateUrl('management_visit_edit', array('id'=>$visit->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Visit:add.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'management'
        ));
    }

    /**
     * @Route("/{id}/edit")
     * @Security("has_role('ROLE_MANAGER')")
     */
    public function editAction(Visit $visit, Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Management", $this->get("router")->generate("management_default_index"));
        $breadcrumbs->addItem("Visits", $this->get("router")->generate("management_visit_index"));
        $breadcrumbs->addItem("Edit", $this->get("router")->generate("management_visit_edit",array('id'=>$visit->getId())));

        $em = $this->getDoctrine()->getManager();

        $visitAccepted = $visit->getAccepted();

        $form = $this->createForm(VisitType::class, $visit);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($visit);

            if ($visit->getDocumentFile()) {
                $uploadableManager = $this->get('stof_doctrine_extensions.uploadable.manager');
                $uploadableManager->markEntityToUpload($visit, $visit->getDocumentFile());
            }

            $em->flush();

            $event = new VisitEvent($visit, 'edit');
            $dispatcher = $this->get('event_dispatcher'); 
            $dispatcher->dispatch('producer.events.visitPersisted', $event);
            if ($visit->getAccepted() !== null && $visitAccepted === null) {
                $dispatcher->dispatch('producer.events.visitCompleted', $event);
            }

            if ($form->get('saveAndClose')->isClicked()) {
                $url = $this->generateUrl('management_visit_index');
            }else{
                $url = $this->generateUrl('management_visit_edit', array('id'=>$visit->getId()));
            }
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ManagementBundle:Visit:edit.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'management'
        ));
    }
}
