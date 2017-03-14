<?php

namespace ProducerBundle\Event;

use ProducerBundle\Entity\Visit;
use Symfony\Component\EventDispatcher\Event;
use ProducerBundle\Event\Exceptions\WrongTypeException;

class VisitEvent extends Event
{
    protected $visit;
    protected $action;

    protected $actions = array(
        'add', 'edit', 'delete',
    );

    public function __construct(Visit $visit, $action)
    {
        $this->visit = $visit;

        if (!is_string($action)) {
            throw new WrongTypeException('action', 'string', gettype($action), __FILE__, __LINE__);
        }
        if (!in_array($action, $this->actions)) {
            throw new WrongActionException($this->actions, $action, __FILE__, __LINE__);
        }
        $this->action = $action;
    }

    public function getVisit()
    {
        return $this->visit;
    }

    public function getAction()
    {
        return $this->action;
    }
}
