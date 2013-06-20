<?php
namespace Cerad\Bundle\PersonBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CreateController extends Controller
{
    public function createAYSORefereeAction(Request $request)
    {
        // New person
        $personManager = $this->get('cerad_person.manager');
        $person = $personManager->newPerson();
        
        // If signed in then fill in some stuff?
        $account = $this->getUser();
        if (is_object($account))
        {
            $person->setName ($account->getName ());
            $person->setEmail($account->getEmail());
        }
        else $account = null;
        
        // Form stuff
        $form = $this->createForm($this->get('cerad_person.ayso_referee_create.formtype'),$person);
        $form->handleRequest($request);

        if ($form->isValid() && 1) 
        { 
            // Process it
            $person = $form->getData();
            
            // Xfer identifier to cert
            $identifier = $person->getLeagueAYSOVolunteer()->getIdentifier();
            $person->getCertAYSOReferee()->setidentifier($identifier);
            
            // Do we already have a person with the same aysoid?
            $personx = $personManager->loadPersonForLeagueIdentifier($identifier);
            if ($personx)
            {   
                /* ====================================
                 * Could send an event or email
                 * Could check for updated info
                 * Need some way to verify it's really them
                 * 
                 * For now, just use it
                 */
                $person = $personx;
            }
            // Persist
            $personManager->updatePerson($person,true);
            
            // This should also be a message but for now attach to account
            if ($account)
            {
                if (!$account->getPersonGuid())
                {
                    $account->setPersonGuid($person->getId());
                    $accountManager = $this->get('cerad_account.manager');
                    $accountManager->updateUser($account,true);
                }
            }
            // Back to home
            $response = $this->redirect($this->generateUrl('cerad_tourn_home'));
            return $response;
        }
        $tplData = array();
        $tplData['form']    = $form->createView();
        $tplData['account'] = $account;
        $tplData['person' ] = $person;
        return $this->render('@CeradPerson/AYSO/referee/create/index.html.twig', $tplData);
    }
}
?>
