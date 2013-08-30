<?php

namespace Cerad\Bundle\PersonBundle\EventListener;

use Cerad\Bundle\CommonBundle\CeradCommonEvents;

use Cerad\Bundle\CommonBundle\Event\PersonLoadEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PersonEventListener implements EventSubscriberInterface
{
    protected $personRepo;
    
    public static function getSubscribedEvents()
    {
        return array(
            CeradCommonEvents::PERSON_LOAD => 'onPersonLoad',
        );
    }
    public function __construct($personRepo)
    {
        $this->personRepo = $personRepo;
    }
    
    /* =========================================
     * Maybe the repo should have
     * findByIdOrIdentifierValue
     */
    public function onPersonLoad(PersonLoadEvent $event)
    {
        // Guid or an id
        $identifier = $event->getIdentifier();
        if (!$identifier) return;
        
        // Guid
        $person = $this->personRepo->find($identifier);
        if ($person)
        {
            $event->setPerson($person);
            return;
        }
        // Identifier
        $person = $this->personRepo->findByIdentifierValue($identifier);
        if ($person)
        {
            $event->setPerson($person);
            return;
        }

        // Done
        return;        
    }
}
