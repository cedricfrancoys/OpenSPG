<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $nodes = $em->getRepository('NodeBundle:Node')->findAll();

        $upcomingVisits = $em->getRepository('ProducerBundle:Visit')->getUpcoming();
        $latestVisits = $em->getRepository('ProducerBundle:Visit')->getLatest();
        $pendingApprovalVisits = $em->getRepository('ProducerBundle:Visit')->getPendingApproval();
        $properties = $em->getRepository('ProducerBundle:Property')->getPropertiesWithLocation();

        $stocks = $em->getRepository('ProducerBundle:Stock')->getNewest();

        $news = $em->getRepository('NewsBundle:News')->getNewest();

        $data = array(
            'products' => $stocks,
            'nodes' => $nodes,
            'menu' => 'home',
            'upcomingVisits' => $upcomingVisits,
            'latestVisits' => $latestVisits,
            'news' => $news,
            'pendingApprovalVisits' => $pendingApprovalVisits,
            'properties' => $properties,
        );

        return $this->render('AppBundle:Default:index.html.twig', $data);
    }

    /**
     * @Route("/sendTestEmail")
     */
    public function sendTestEmailAction()
    {
        $mailer = $this->get('mailer');

        $message = \Swift_Message::newInstance()
            ->setSubject('Test Email')
            ->setFrom('hello@raac.tobeonthe.net')
            ->setTo('mhauptma73@gmail.com')
            ->setBody(
                'Simple teste message',
                'text/html'
            )
        ;
        $mailer->send($message);
    }
}
