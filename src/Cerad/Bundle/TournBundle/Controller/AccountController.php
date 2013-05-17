<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AccountController extends Controller
{
    public function createAction(Request $request)
    {   
        $manager = $this->get('cerad_tourn.schedule.manager');
        
        $item = array
        (
            'userName'         => null,
            'userPass'         => null,
            
            'aysoVolunteerId'  => null,
            'aysoRegionId'     => null,
            'aysoRefereeBadge' => null,
            
            'personFirstName'  => null,
            'personLastName'   => null,
            'personNickName'   => null,
            'personPhone'      => null,
            'personEmail'      => null,
        );
                
        // Build the form
        $formType = $this->get('cerad_tourn.account.create.formtype');
        
        // The form itself
        $form = $this->createForm($formType,$item);
        
        // Check post
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);

            if ($form->isValid())
            {
                $item = $form->getData(); print_r($item); //die( 'POSTED');
                
                $account = $this->create($item);
                //return $this->redirect($this->generateUrl('cerad_tourn_schedule_referee_list'));
            }
            //else die("Not valid");
        }
        $tplData = array();
        $tplData['form'] = $form->createView();
        
        return $this->render('@CeradTourn/account/create.html.twig', $tplData);
    }
    public function editAction(Request $request, $id = 0)
    {
        return $this->redirect($this->generateUrl('cerad_tourn_home'));
    }
    /* =========================================================================
     * This is where the actual account/person gets created
     */
    protected function create($item)
    {
        $personManager  = $this->get('cerad_person.manager');
        $accountManager = $this->get('fos_user.user_manager');
        
        // Canacolize email
        $item['personEmail'] = $accountManager->canEmail($item['personEmail']);
        
        // Get the person
        $person = $personManager->createOrLoadPerson($item);
        if (!$person) return null;
        
        // Need to make sure the email is not in use for an existing user
        // Or use the inputed email which implies a mismatch
        //$account = $accountManager->findUserByUsernameOrEmail($person->getEmail());
        //if ($account)
        //{
            //return null;
        //}
        // The account
        $account = $accountManager->createUser();

        // User name stays the same
        $account->setUsername     ($item['userName']);
        $account->setPlainPassword($item['userPass']);
        
        // Which email?
        $account->setEmail($person->getEmail()); 
        $account->setEmail($item['personEmail']);
        
        $account->setName ($person->getName());
        
        $account->setPersonGuid($person->getId());
        
        $account->setEnabled(true);
    
        // Persist
        $personManager->persist($person);
        $personManager->flush();
        
        $accountManager->updateUser($account,true);
        
        return $account;
        
    }
}
?>
