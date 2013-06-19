<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Used by AuthenticationListener to log user in with LoginManager
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;

class CreateController extends Controller
{
    public function createAction(Request $request)
    {
        // Grab a new account
        $manager = $this->get('fos_user.user_manager');
        $account = $manager->createUser();
        
        // See if came in from a provider
        $session = $request->getSession();
        $profile = $session->get('cerad_janrain_profile');
        if ($profile)
        {
            // Just because
            $providerName = $profile->getProviderName();
            $displayName  = $profile->getDisplayName();
            $identifier   = $profile->getIdentifier();
            $data         = $profile->getData();
            
            // Always have one identifier
            $identifierEntity = $manager->createIdentifier($providerName,$displayName,$identifier,$data);
            $manager->addIdentifierToUser($account,$identifierEntity);
            
            // Might have two
            $identifier2 = $profile->getIdentifier2();
            if ($identifier2)
            {
                $identifier2Entity = $manager->createIdentifier($providerName,$displayName,$identifier2,$data);
                $manager->addIdentifierToUser($account,$identifier2Entity);
            }
            
            // Preset account stuff
            $account->setName    ($displayName);
            $account->setEmail   ($profile->getEmail());
            $account->setUsername($profile->getUsername());
        }
        else $profile = null;
        
        // Form stuff
        $form = $this->createForm($this->get('cerad_account.create.formtype'),$account);
        $form->handleRequest($request);

        if ($form->isValid() && 1) 
        { 
            // Don't worry about email validation for now
            $account->setEnabled(true);
            $manager->updateUser($account,true);
            
            // Want to tweak this
            $response = $this->redirect($this->generateUrl('cerad_tourn_home'));
            
            // This will sign the user in via the AuthenticationListener
            $dispatcher = $this->get('event_dispatcher');
            $dispatcher->dispatch
            (
                FOSUserEvents::REGISTRATION_COMPLETED,
                new FilterUserResponseEvent($account, $request, $response)
            );
            return $response;
        }
        $tplData = array();
        $tplData['form']    = $form->createView();
        $tplData['profile'] = $profile;
        return $this->render('@CeradAccount/create/index.html.twig', $tplData);
    }
}
?>
