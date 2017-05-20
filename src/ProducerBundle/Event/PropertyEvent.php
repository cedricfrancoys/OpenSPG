<?php

namespace ProducerBundle\Event;

use ProducerBundle\Entity\Property;
use Symfony\Component\EventDispatcher\Event;
use ProducerBundle\Event\Exceptions\WrongTypeException;

class PropertyEvent extends Event
{
    protected $property;
    protected $action;

    protected $actions = array(
        'add', 'edit', 'delete', 'event',
    );

    /**
     * Constructor of the class
     *
     * @var Property $property
     * @var string $action
     *
     * @return void
     */
    public function __construct(Property $property, $action)
    {
        $this->property = $property;

        if (!is_string($action)) {
            throw new WrongTypeException('action', 'string', gettype($action), __FILE__, __LINE__);
        }
        if (!in_array($action, $this->actions)) {
            throw new WrongActionException($this->actions, $action, __FILE__, __LINE__);
        }
        $this->action = $action;
    }

    /**
     * Returns the property object
     *
     * @return Property
     */
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * Returns the user object associated with the property
     *
     * @return \UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->property->getMember()->getUser();
    }

    /**
     * Returns the action that is beeing used
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
