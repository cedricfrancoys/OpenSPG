<?php
namespace ProducerBundle\Event;
 
use Symfony\Component\EventDispatcher\Event;

class VisitEventSubscriber
{
    protected $em;

    public function onVisitSaved(Event $event)
    {
        $visit = $event->getVisit();
        $producer = $visit->getProducer();
        $action = $event->getAction();

        if ($action == 'add' && $visit->getAccepted()) {
            $producer->setActiveAsProducer(true);
        }elseif ($action == 'add' && $visit->getAccepted() === false) {
            $producer->setActiveAsProducer(false);
        }elseif ($action == 'edit') {
            $visits = $this->em->getRepository('ProducerBundle:Visit')->findBy(array('Producer'=>$producer), array('visitDate'=>'DESC'));
            if ($visits[0] == $visit) {
                if ($visit->getAccepted()) {
                    $producer->setActiveAsProducer(true);
                }elseif($visit->getAccepted() === false){
                    $producer->setActiveAsProducer(false);
                }
            }
        }

        $this->em->persist($producer);
        $this->em->flush();
    }

    public function setEntityManager($em)
    {
        $this->em = $em;
    }
}