<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

// Used by AuthenticationListener to log user in with LoginManager
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FilterUserResponseEvent;

class AccountController extends Controller
{
    public function createAction(Request $request)
    {   
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
        // print_r($_POST);
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);

            if ($form->isValid())
            {
                // var_dump($_POST);
                $item = $form->getData(); // print_r($item); //die( 'POSTED');
                
                $account = $this->create($item);
                if ($account)
                {
                    $response = $this->redirect($this->generateUrl('cerad_tourn_home'));
                    
                    $dispatcher = $this->get('event_dispatcher');
                    
                    $dispatcher->dispatch(
                            FOSUserEvents::REGISTRATION_COMPLETED, 
                            new FilterUserResponseEvent($account, $request, $response)
                    );
                    return $response;
                }
                //return $this->redirect($this->generateUrl('cerad_tourn_schedule_referee_list'));
            }
            //else die("Not valid " . $form->getErrorsAsString());
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
    /*
     * array(2) { 
     * ["cerad_tourn_account_create"]=> array(11) { 
     *   ["userName"]=> string(10) "ahundiak03" 
     *   ["personEmail"]=> string(20) "ahundiak@gmail.com03" 
     *   ["userPass"]=> array(2) { ["pass1"]=> string(3) "zzz" ["pass2"]=> string(3) "zzz" } 
     *   ["aysoVolunteerId"]=> string(8) "12340003" 
     *   ["aysoRegionId"]=> string(3) "123" 
     *   ["aysoRefereeBadge"]=> string(8) "Advanced" 
     *   ["personFirstName"]=> string(6) "Arthur" 
     *   ["personLastName"]=> string(7) "Hundiak" 
     *   ["personNickName"]=> string(5) "Art03" 
     *   ["personPhone"]=> string(12) "256.457.5943" 
     *   ["_token"]=> string(40) "050600bffb4b6be7324efb66016e7df9903b4c8c" } 
     *   ["accountCreate"]=> string(20) "Create Zayso Account" } Valid
     */
    /*
    Array ( 
     * [cerad_tourn_account_create] => Array ( 
     *   [userName] => ahundiak03 
     *   [personEmail] => ahundiak@gmail.com03 
     *   [userPass] => Array ( [pass1] => zzz [pass2] => zzz ) 
     *   [aysoVolunteerId] => 12340003 
     *   [aysoRegionId] => 894 
     *   [aysoRefereeBadge] => Advanced 
     *   [personFirstName] => Arthur 
     *   [personLastName] => Hundiak 
     *   [personNickName] => Art03 
     *   [personPhone] => 256.457.5943 
     *   [_token] => 57b328cb17bd0e1332b81157ddc906a51f16ee63 ) 
     *   [accountCreate] => Create Zayso Account ) 
      */      
}
?>
