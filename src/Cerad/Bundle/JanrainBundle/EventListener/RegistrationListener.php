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
    protected $userManager;
    
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_INITIALIZE =>       'onRegistrationInitialize',
            FOSUserEvents::REGISTRATION_SUCCESS    => array('onRegistrationSuccess',10),
        );
    }
    public function __construct($userManager)
    {
        $this->userManager = $userManager;
    }
    public function onRegistrationSuccess(FormEvent $event)
    {
        /* =================================================
         * See if have a janrain profile
         * TODO: Make sure the profile goes aways when signing out
         */
        $session = $event->getRequest()->getSession();
        if (!$session->has('cerad_janrain_profile')) return;
   
        // Get the stuff
        $profile = $session->get('cerad_janrain_profile');
        $user    = $event->getForm()->getData();
        
        // Want to add one or possibly two account_identifier objects
        $identifier = $this->userManager->createIdentifier($profile->getProviderName(),$profile->getIdentifier(),$profile->getData());
        $this->userManager->addIdentifierToUser($user,$identifier);
        
        if ($profile->getIdentifier2())
        {
            $identifier2 = $this->userManager->createIdentifier($profile->getProviderName(),$profile->getIdentifier2(),$profile->getData());
            $this->userManager->addIdentifierToUser($user,$identifier2);
        }
        /* ===============================================
         * If have a verified email then no need to confirm
         */
        $verifiedEmail = $profile->getVerifiedEmail();
        if (!$verifiedEmail) return;
        
        if ($verifiedEmail == $user->getEmail()) $event->stopPropagation();
        
        // Done
        return;
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
