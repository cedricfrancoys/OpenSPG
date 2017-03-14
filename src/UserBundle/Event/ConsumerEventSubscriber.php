<?php

namespace UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Translation\TranslatorInterface;

class ConsumerEventSubscriber
{
    protected $em;
    protected $mailer;
    protected $twig;
    protected $translator;

    public function onConsumerCreated(Event $event)
    {
        $consumer = $event->getConsumer();

        $subscribers = $this->getSubscribedUsers();

        foreach ($subscribers as $subscriber) {
            $this->sendEmail($subscriber, $consumer);
        }
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }

    public function setMailer($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Set the translator.
     *
     * @param TranslatorInterface $trans
     */
    public function setTranslator(TranslatorInterface $trans)
    {
        $this->translator = $trans;
    }

    /**
     * Set twig.
     *
     * @param Twig_Environment $twig
     */
    public function setTwig(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    protected function getSubscribedUsers()
    {
        $em = $this->em;

        return $em->getRepository('UserBundle:User')->findBy(array('receiveEmailNewConsumer' => true));
    }

    protected function sendEmail($subscriber, $consumer)
    {
        $trans = $this->translator;
        $tpl = $this->twig;

        $consumerUser = $consumer->getUser();

        $message = \Swift_Message::newInstance()
            ->setSubject($trans->trans('New.consumer.email.subject', array(), 'user'))
            ->setFrom('noreply@raac.tobeonthe.net')
            ->setTo($subscriber->getEmail())
            ->setBody(
                $tpl->render(
                    'UserBundle:Emails:newConsumer.html.twig',
                    array(
                        'name' => $subscriber->getName(),
                        'consumer_name' => $consumerUser->getName().' '.$consumerUser->getSurname(),
                        'consumer_id' => $consumer->getId(),
                        'profile_path' => ($subscriber->getProducer()) ? 'producer_member_profile' : 'consumer_member_profile',
                    )
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}
