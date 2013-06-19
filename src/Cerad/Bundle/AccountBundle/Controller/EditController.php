<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Used by AuthenticationListener to log user in with LoginManager
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;

class EditController extends Controller
{
    public function editAction(Request $request, $id)
    {
        // Grab a new account
        $manager = $this->get('fos_user.user_manager');
        $account = $manager->findUserBy(array('id' => $id));
                
        // Form stuff
        $form = $this->createForm($this->get('cerad_account.edit.formtype'),$account);
        $form->handleRequest($request);

        if ($form->isValid() && 1) 
        { 
            // Don't worry about email validation for now
            $manager->updateUser($account,true);
            
            // Want to tweak this
            $response = $this->redirect($this->generateUrl('cerad_account_home'));
            return $response;
            
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
        $tplData['account'] = $account;
        return $this->render('@CeradAccount/edit/index.html.twig', $tplData);
    }
}
?>
