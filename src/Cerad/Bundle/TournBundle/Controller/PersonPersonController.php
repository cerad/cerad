<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/* ===============================
 * Older code, try using @CeradPerson controller?
 * Still want to customize for adding ayso folks
 */
class PersonPersonController extends Controller
{
    public function getAccountPerson()
    { 
        // Must have a person
        $account = $this->getUser();
        if (!is_object($account)) return null;
        
        $person = $account->getPerson();
        if (!is_object($person)) return null;

        return $person;
    }
    public function addAction(Request $request)
    {
        // Must have an account and person
        $master = $this->getAccountPerson();
        if (!$master)
        {
            return $this->redirect($this->generateUrl('cerad_tourn_welcome'));  
        }
        // Empty new person, matches what is in account create
        $item = array
        (
            'role'             => 'Family',
            
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
        $formType = $this->get('cerad_tourn.person.person.add.formtype');
        $form = $this->createForm($formType,$item);
        
        // Check post
        if ($request->isMethod('POST'))
        {
            // Submit with a response will be depreciated in 3.x
            // $form->submit($request->request->get($form->getName()));
            $form->submit($request);

            if ($form->isValid())
            {
                $item = $form->getData();
                
                // Make the actual account
                $personPerson = $this->addProcess($master,$item);
                if ($personPerson)
                {
                    return $this->redirect($this->generateUrl('cerad_tourn_home'));
                }
                // Need to handle errors
                // else return $this->redirect($this->generateUrl('cerad_tourn_account_create_failure'));
            }
        }        
        // Just display
        $tplData = array();
        $tplData['form']    = $form->createView();
        $tplData['account'] = $this->getUser();
        $tplData['master']  = $master;
        $tplData['project'] = $this->get('cerad_tourn.project');
        
        return $this->render('@CeradTourn/person/person/add.html.twig', $tplData);
    }
    /* ==================================================
     * This should probably be moved to person manager
     */
    public function addProcess($master,$item)
    {
        // Get the slave
        $personManager  = $this->get('cerad_person.manager');
        $slave = $personManager->createOrLoadPerson($item);
        if (!$slave) return null;
        
        // Make the PersonPerson
        $personPerson = $personManager->newPersonPerson();
        $personPerson->setRole($item['role']);
        $personPerson->setMaster($master);
        $personPerson->setSlave ($slave);
        $personPerson->setVerified('No');
        $master->addPerson($personPerson);
      
        $personManager->persist($master);
        $personManager->persist($slave);
        $personManager->persist($personPerson);
        
        try
        {
            $personManager->flush();
        }
        catch (\Exception $e)
        {
            return null;
        }
        return $personPerson;
    }
}

?>
