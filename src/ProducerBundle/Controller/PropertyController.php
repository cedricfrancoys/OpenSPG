<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use ProducerBundle\Entity\Property;
use ProducerBundle\Form\PropertyType;

/**
 * @Route("/productor/propiedad")
 * @Security("has_role('ROLE_PRODUCER')")
 */
class PropertyController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('My account', $this->get('router')->generate('producer_member_index'));
        $breadcrumbs->addItem('Properties', $this->get('router')->generate('producer_property_index'));

        $properties = $this->getUser()->getProducer()->getProperties();

        return $this->render('ProducerBundle:Property:index.html.twig', array(
            'properties' => $properties,
            'menu' => 'account',
        ));
    }

    /**
     * @Route("/add")
     * @Security("has_role('ROLE_PRODUCER') or has_role('ROLE_CONSUMER')")
     */
    public function addAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        if ($this->get('security.authorization_checker')->isGranted('ROLE_PRODUCER')) {
            $breadcrumbs->addItem('My account', $this->get('router')->generate('producer_member_index'));
            $breadcrumbs->addItem('Properties', $this->get('router')->generate('producer_property_index'));
            $breadcrumbs->addItem('Add', $this->get('router')->generate('producer_property_add'));
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_CONSUMER')) {
            $breadcrumbs->addItem('My account', $this->get('router')->generate('consumer_member_index'));
            $breadcrumbs->addItem('Add property', $this->get('router')->generate('producer_property_add'));
        }

        $property = new Property();

        $form = $this->createForm(PropertyType::class, $property);
        // $form->setData($user);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $member = $em->getRepository('ProducerBundle:Member')->getUser($this->getUser());
            $property->setMember($member);

            $em->persist($property);
            $em->flush();

            $url = $this->generateUrl('producer_property_edit', array('id' => $property->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ProducerBundle:Property:edit.html.twig', array(
            'form' => $form->createView(),
            'menu' => 'account',
        ));
    }

    /**
     * @Route("/{id}/edit")
     */
    public function editAction(Property $property, Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('My account', $this->get('router')->generate('producer_member_index'));
        $breadcrumbs->addItem('Properties', $this->get('router')->generate('producer_property_index'));
        $breadcrumbs->addItem('Edit', $this->get('router')->generate('producer_property_edit', array('id' => $property->getId())));

        $em = $this->getDoctrine()->getManager();

        if ($property->getMember()->getUser() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(PropertyType::class, $property);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($property);
            $em->flush();

            $url = $this->generateUrl('producer_property_edit', array('id' => $property->getId()));
            $response = new RedirectResponse($url);

            return $response;
        }

        return $this->render('ProducerBundle:Property:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
