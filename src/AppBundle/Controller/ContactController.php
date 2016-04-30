<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use AppBundle\Entity\Contact;
use AppBundle\Form\ContactType;

use AppBundle\Util\Util;

class ContactController extends Controller
{
    /**
     * @Route("/contacto")
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("Contact", $this->get("router")->generate("app_contact_index"));

        $em = $this->getDoctrine()->getManager();

        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $contact->setReceived(new \DateTime());
            $em->persist($contact);
            $em->flush();

            Util::sendReceivedMail($contact, $this->get('translator'), $this->get('mailer'), $this->get('twig'));

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('Your contact request has been sent!', array(), 'contact')
            );

            $url = $this->generateUrl('app_contact_index');
            $response = new RedirectResponse($url);

            return $response;
        }

        $data = array(
            'form' => $form->createView(),
            'menu' => 'contact'
        );

        return $this->render('AppBundle:Contact:index.html.twig', $data);
    }

    // protected function sendReceivedMail(Contact $contact)
    // {
    //     $trans = $this->get('translator');

    //     $subject = $trans->trans('New contact request received', array(), 'contact');

    //     $message = \Swift_Message::newInstance()
    //         ->setSubject($subject)
    //         ->setFrom('hello@raac.es')
    //         ->setTo('mhauptma73@gmail.com')
    //         ->setBody(
    //             $this->renderView(
    //                 'AppBundle:Contact:admin_mail.html.twig',
    //                 array(
    //                     'contact' => $contact
    //                 )
    //             ),
    //             'text/html'
    //         )
    //     ;
    //     $this->get('mailer')->send($message);
    // }
}
