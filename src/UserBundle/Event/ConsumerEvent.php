<?php

namespace UserBundle\Event;

use ConsumerBundle\Entity\Member;
use Symfony\Component\EventDispatcher\Event;

class ConsumerEvent extends Event
{
    protected $consumer;

    public function __construct(Member $consumer)
    {
        $this->consumer = $consumer;
    }

    public function getConsumer()
    {
        return $this->consumer;
    }
}
