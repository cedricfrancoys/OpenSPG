<?php

namespace ConsumerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Entity\Contact;
use ConsumerBundle\Form\ContactType;
use ConsumerBundle\Entity\Member;

/**
 * @Route("/consumdires/contacto")
 */
class ContactPublicController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $breadcrumbs = $this->get('white_october_breadcrumbs');
        $breadcrumbs->addItem('Home', $this->get('router')->generate('homepage'));
        $breadcrumbs->addItem('Consumers', $this->get('router')->generate('consumer_consumer_index'));
        $breadcrumbs->addItem('Contact', $this->get('router')->generate('consumer_contactpublic_index'));

        $em = $this->getDoctrine()->getManager();

        $contact = new Contact();

        if ($request->get('consumer_id')) {
            $consumer = $em->getRepository('ConsumerBundle:Member')->find($request->get('consumer_id'));
            $contact->setReceiver($consumer->getUser());
        } else {
            $consumer = null;
        }

        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $contact->setReceived(new \DateTime());
            $em->persist($contact);
            $em->flush();

            $this->sendReceivedMail($contact, $consumer);

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
            'menu' => 'consumer',
        );

        return $this->render('ConsumerBundle:ContactPublic:index.html.twig', $data);
    }

    protected function sendReceivedMail(Contact $contact, Member $consumer = null)
    {
        $trans = $this->get('translator');

        $subject = $trans->trans('New contact request received', array(), 'contact');
        if ($consumer) {
            $receiver = $consumer->getUser()->getEmail();
        }
        if (!$receiver) {
            return;
        }

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('hello@raac.tobeonthe.net')
            ->setTo($receiver)
            ->setBody(
                $this->renderView(
                    'ConsumerBundle:ContactPublic:contact_mail.html.twig',
                    array(
                        'contact' => $contact,
                    )
                ),
                'text/html'
            )
        ;
        $this->get('mailer')->send($message);
    }
}
