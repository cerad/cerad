<?php
namespace Cerad\Bundle\CommonBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class PersonLoadEvent extends Event
{
    private $request;
    private $identifier;
    private $person;
    
    public function __construct($identifier, Request $request = null)
    {
        $this->identifier = $identifier;
        $this->request    = $request;
    }
    public function getPerson()      { return $this->person;     }
    public function getRequest()     { return $this->request;    }
    public function getIdentifier()  { return $this->identifier; }
    
    public function setPerson($item) { $this->person = $item;    }

}
