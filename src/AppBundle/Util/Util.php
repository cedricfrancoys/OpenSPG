<?php

namespace AppBundle\Util;

use Symfony\Component\Translation\DataCollectorTranslator;
use AppBundle\Entity\Contact;

class Util
{
    public static function sendReceivedMail(Contact $contact, DataCollectorTranslator $trans, \Swift_Mailer $mailer, \Twig_Environment $template)
    {
        $subject = $trans->trans('New contact request received', array(), 'contact');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom('hello@raac.es')
            ->setTo('mhauptma73@gmail.com')
            ->setBody(
                $template->render(
                    'AppBundle:Contact:admin_mail.html.twig',
                    array(
                        'contact' => $contact,
                    )
                ),
                'text/html'
            )
        ;
        $mailer->send($message);

        return true;
    }
}
