<?php
namespace Cerad\Bundle\TournBundle\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/* ========================================================
 * Primarily deals with the case where an account has been created (via social netword)
 * But no person
 */
class PersonController extends Controller
{
    public function editAction(Request $request, $id = null)
    {
        /* ====================================================
         * Must have an account
         */
        $account = $this->getUser();
        if (!is_object($account)) return $this->redirect($this->generateUrl('cerad_tourn_welcome')); ;
       
        /* ====================================================
         * Grab the person relying on the auto create process to make sure have one
         */
        $person = $account->getPerson();
      //$league = $person->getVolunteerAYSO();
      //$cert   = $person->getCertRefereeAYSO();
        
        // Email is a bit tricky
        if (!$person->getEmail()) $person->setEmail($account->getEmailCanonical());
        
        // Might want to try a bit of majic with pulling names from social network
        
        // Build the form
        $formType = $this->get('cerad_tourn.account.person.edit.formtype');
        $form = $this->createForm($formType,$person);
        
        // New 2.3 style
        $form->handleRequest($request);
        
        // Check post
        if ($form->isValid())
        {
            $person = $form->getData();
            
            $this->editProcess($account,$person);
            // Need to handle account creation errors or at least send an email
            return $this->redirect($this->generateUrl('cerad_tourn_home'));
        }
        $tplData = array();
        $tplData['form'] = $form->createView();
        return $this->render('@CeradTourn/person/edit.html.twig', $tplData);
    }
    /* =========================================================================
     * Allow for the fact that user might be existing
     */
    protected function editProcess($account,$person)
    {
        $accountManager = $this->get('fos_user.user_manager');
        $personManager = $this->get('cerad_person.manager');

        // See if have one
        $identifier = $person->getVolunteerAYSO()->getIdentifier();
        $personLeague = $personManager->loadPersonLeagueForIdentifier($identifier);
        if ($personLeague)
        {
            $personx = $personLeague->getPerson();
            if ($person->getId() != $personx->getId())
            {
                // Account did not have a person, use existing person
                $account->setPersonGuid($personx->getId());
                $accountManager->updateUser($account,true);
                return true;
            }
            /* =====================================================
             * This needs more work
             * If the user changed their aysoid then we will have problems
             * The eamil change could also be an issue
             * 
             * It's possible that for this controller we don't want to do anything
             */
            return false;
            $personManager->flush();
            return true;
        }
        
        // Cleanup a few things, maybe need updatePerson
        $person->genName();
        $person->getCertRefereeAYSO()->setIdentifier($identifier);
        $person->getVolunteerAYSO()->setMemId(substr($identifier,5)); // This needs to go away
         
        // Always have primary person person
        $personPerson = $personManager->newPersonPerson();
        $personPerson->setRolePrimary();
        $personPerson->setMaster($person);
        $personPerson->setSlave ($person);
        $personPerson->setVerified('Yes');
        $person->addPerson($personPerson);
        
        // And persist
        $personManager->persist($person);
        $personManager->flush();
        
        // Brand new person, attach to account
        $account->setPersonGuid($person->getId());
        $accountManager->updateUser($account,true);
   } 
}
?>
