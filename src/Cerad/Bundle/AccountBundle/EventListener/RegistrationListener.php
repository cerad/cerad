<?php
namespace Cerad\Bundle\AccountBundle\EventListener;

//  Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use FOS\UserBundle\FOSUserEvents;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;

use FOS\UserBundle\Event\FilterUserResponseEvent;

/// FOS\UserBundle\Event\GetResponseUserEvent;

/* ==================================================================
 * This is just an example of how to listen for events
 */
class RegistrationListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array
        (
            FOSUserEvents::REGISTRATION_INITIALIZE => 'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS    => 'onRegistrationSuccess',
            FOSUserEvents::REGISTRATION_COMPLETED  => 'onRegistrationCompleted',
            FOSUserEvents::REGISTRATION_CONFIRM    => 'onRegistrationConfirm',
            FOSUserEvents::REGISTRATION_CONFIRMED  => 'onRegistrationConfirmed',
        );
    }
    public function onRegistrationInitialize(UserEvent $event)
    {
      //echo sprintf("### onRegistrationInitialize\n");
    }    
    public function onRegistrationSuccess(FormEvent $event)
    {
      //echo sprintf("### onRegistrationSuccess\n");
    }
    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
      //echo sprintf("### onRegistrationCompleted\n");
    }
}
?>
