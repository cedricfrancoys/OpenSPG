<?php

namespace ConsumerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use AppBundle\Entity\Contact;
use ConsumerBundle\Form\ContactType;
use ConsumerBundle\Form\ReplyContactType;

/**
 * @Route("/members/consumidores/contacto")
 */
class ContactController extends Controller
{
    /**
     * @Route("/", options={"expose":true})
     @Security("has_role('ROLE_CONSUMER')")
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("consumer_member_index"));
        $breadcrumbs->addItem("Contacts", $this->get("router")->generate("consumer_contact_index"));

        $em = $this->getDoctrine()->getManager();

        $contacts = $em->getRepository('AppBundle:Contact')->findBy(array('Sender'=>$this->getUser()));

        $data = array(
            'contacts' => $contacts,
            'menu' => 'account'
        );

        return $this->render('ConsumerBundle:Contact:index.html.twig', $data);
    }

    /**
     * @Route("/{id}")
     @Security("has_role('ROLE_CONSUMER')")
     */
    public function showAction(Request $request, Contact $contact)
    {
        $breadcrumbs = $this->get("white_october_breadcrumbs");
        $breadcrumbs->addItem("Home", $this->get("router")->generate("homepage"));
        $breadcrumbs->addItem("My account", $this->get("router")->generate("consumer_member_index"));
        $breadcrumbs->addItem("Contacts", $this->get("router")->generate("consumer_contact_index"));
        $breadcrumbs->addItem("Show", $this->get("router")->generate("consumer_contact_show",array('id'=>$contact->getId())));

        if ($contact->getSender() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getManager();

        $replys = $em->getRepository('AppBundle:Contact')->findBy(array('Parent'=>$contact));

        $replyContact = new Contact();
        $replyContact->setParent($contact);
        $replyContact->setSender($this->getUser());
        $replyContact->setName('');
        $replyContact->setEmail('');

        $form = $this->createForm(ReplyContactType::class, $replyContact);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $replyContact->setSent(new \DateTime());

            $em->persist($replyContact);
            $em->flush();

            $this->sendMail($contact, $replyContact);

            $session = $this->get('session');
            $trans = $this->get('translator');

            // add flash messages
            $session->getFlashBag()->add(
                'success',
                $trans->trans('Reply has been sent!', array(), 'contact')
            );

            $response = new JsonResponse(json_encode($replyContact, true));

            return $response;
        }

        $data = array(
            'contact' => $contact,
            'form' => $form->createView(),
            'menu' => 'account',
            'replys' => $replys
        );

        return $this->render('ProducerBundle:MemberContact:show.html.twig', $data);
    }

    protected function sendMail(Contact $contact, Contact $replyContact)
    {
        $trans = $this->get('translator');

        $subject = $trans->trans('Reply to your contact request', array(), 'contact');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('hello@raac.es')
            ->setTo($contact->getEmail())
            ->setBody(
                $this->renderView(
                    'ProducerBundle:MemberContact:producer_reply_mail.html.twig',
                    array(
                        'contact' => $contact,
                        'reply_contact' => $replyContact
                    )
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);
    }
}
