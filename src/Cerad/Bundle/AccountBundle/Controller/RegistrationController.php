<?php
namespace Cerad\Bundle\AccountBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Model\UserInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RegistrationController extends Controller
{
    public function registerAction(Request $request)
    {
        // Grab a new user
        $userManager = $this->container->get('fos_user.user_manager');
        $dispatcher  = $this->container->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));
        
        // Form stuff
        $form = $this->createForm($this->get('cerad_account.create.formtype'),$user);
        $form->handleRequest($request);

        if ($form->isValid()) 
        { 
            // Form part was a success, allow system to modify user
            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            // Always pass through user manager
            $userManager->updateUser($user);

            // See if success want to redirect us
            $response = $event->getResponse();
            if (!$response)
            {
                $response = $this->redirect($this->generateUrl('cerad_account_home'));
            }

            // Everything is great, generate the correct redirect
            // And actually sign the user in?
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));
            return $response;
        }
        
        
       // Render
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradAccount/Registration/Register/index.html.twig', $tplData);        
    }
}
?>
