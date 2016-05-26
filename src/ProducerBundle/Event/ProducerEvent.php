<?php
namespace ProducerBundle\Event;

use ProducerBundle\Entity\Member;
 
use Symfony\Component\EventDispatcher\Event;

use ProducerBundle\Event\Exceptions\WrongTypeException;
 
class ProducerEvent extends Event
{
    protected $producer;
    protected $action;

    protected $actions = array(
    	'add', 'edit', 'delete', 'event'
    );

    public function __construct(Member $producer, $action)
    {
        $this->producer = $producer;

        if( !is_string($action) )
        {
        	throw new WrongTypeException('action', 'string', gettype($action), __FILE__, __LINE__);
        }
        if (!in_array($action, $this->actions)) {
        	throw new WrongActionException($this->actions, $action, __FILE__, __LINE__);
        }
        $this->action = $action;
    }

    public function getProducer()
    {
        return $this->producer;
    }

    public function getAction()
    {
        return $this->action;
    }
}