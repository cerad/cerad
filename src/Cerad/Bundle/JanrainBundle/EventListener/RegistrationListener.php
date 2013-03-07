<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cerad\Bundle\JanrainBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;

use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FormEvent;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE =>       'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS    => array('onRegistrationSuccess',10),
        );
    }
    public function onRegistrationSuccess(FormEvent $event)
    {
        $session = $event->getRequest()->getSession();
        if (!$session->has('cerad_janrain_profile')) return;
        
        $profile = $session->get('cerad_janrain_profile');
         
        $verifiedEmail = $profile->getVerifiedEmail();
        if (!$verifiedEmail) return;
        
        $user = $event->getForm()->getData();
        
        if ($verifiedEmail != $user->getEmail()) return;
        
        // Stop it from reaching the email confirmation listener
        $event->stopPropagation();
    }
    public function onRegistrationInitialize(UserEvent $event)
    {
        $session = $event->getRequest()->getSession();
        if (!$session->has('cerad_janrain_profile')) return;
        
        $profile = $session->get('cerad_janrain_profile');
        
        $user = $event->getUser();
        
        $user->setUsername($profile->getUsername());
        $user->setName    ($profile->getName ());
        $user->setEmail   ($profile->getEmail());
        
        // Other good stuff in the profile as well
        
        // echo sprintf("RegistrationInitialize %s %s\n",get_class($user),get_class($profile));
        
        //die();
        
    }
}
