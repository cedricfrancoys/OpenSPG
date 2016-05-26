<?php
namespace ProducerBundle\Event;
 
use Symfony\Component\EventDispatcher\Event;

class VisitEventSubscriber
{
    protected $em;
    protected $producerManager;

    public function onVisitSaved(Event $event)
    {
        $visit = $event->getVisit();
        $producer = $visit->getProducer();
        $action = $event->getAction();

        $this->producerManager->setProducer($producer);

        if ($action == 'add' && $visit->getAccepted()) {
            $this->producerManager->setApproved();
            // $producer->setActiveAsProducer(true);
        }elseif ($action == 'add' && $visit->getAccepted() === false) {
            $producer->setActiveAsProducer(false);
        }elseif ($action == 'edit') {
            $visits = $this->em->getRepository('ProducerBundle:Visit')->findBy(array('Producer'=>$producer), array('visitDate'=>'DESC'));
            if ($visits[0] == $visit) {
                if ($visit->getAccepted()) {
                    $this->producerManager->setApproved();
                    // $producer->setActiveAsProducer(true);
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

    public function setProducerManager($manager)
    {
        $this->producerManager = $manager;
    }
}