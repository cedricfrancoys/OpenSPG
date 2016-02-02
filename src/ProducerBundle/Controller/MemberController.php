<?php

namespace ProducerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use ProducerBundle\Entity\Member;
use ProducerBundle\Form\MemberType;

class MemberController extends Controller
{
    /**
     * @Route("/members/producer/")
     */
    public function indexAction()
    {
        return $this->render('ProducerBundle:Member:index.html.twig');
    }

    /**
     * @Route("/members/producer/profile")
     */
    public function profileAction(Request $request){

    	$profile = new Member();
    	$form = $this->createForm(MemberType::class, $profile);

    	$form->handleRequest($request);

    	if ($form->isSubmitted() && $form->isValid()) {
		    $em = $this->getDoctrine()->getManager();
		    $em->persist($profile);
		    $em->flush();

		    return $this->redirectToRoute('producer_member_profile');
		}


    	return $this->render('ProducerBundle:Member:profile.html.twig', array(
    		'form' => $form->createView()
    	));
    }
}
