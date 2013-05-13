<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

class ScheduleController extends Controller
{
    public function refereeAction(Request $request)
    {   
        $manager = $this->get('cerad_tourn.schedule.manager');
        
        // Build the search parameter information
        $searchData = array();
        
        $searchData['domains']    = array('AYSOA5B');
        $searchData['domainSubs'] = array('Games');
        
        $searchData['ages']    = array();
        $searchData['genders'] = array();
         
        $searchData['levels']  = array();
        $searchData['teams' ]  = array();
        $searchData['fields']  = array();
        
        $searchData['seasons']  = array('SP2013');
        $searchData['sports']   = array('Soccer');
        $searchData['statuses'] = array();
        
        $searchData['dates'] = array('2013-05-17','2013-05-18','2013-05-19');
        
        // Pull from session if nothing was passed
        $sessionSearchData = $request->getSession()->get('ScheduleSearchData');
        
        if ($sessionSearchData) $searchData = array_merge($searchData,json_decode($sessionSearchData,true));
   
        // Build the form
        $searchFormType = $this->get('cerad_tourn.schedule.referee.search.formtype');
        
        // The form itself
        $searchForm = $this->createForm($searchFormType,$searchData);
        
        // Check post
        if ($request->getMethod() == 'POST')
        {
            $searchForm->bindRequest($request);

            if ($searchForm->isValid())
            {
                $searchData = $searchForm->getData(); // print_r($searchData); die( 'POSTED');
                
                $request->getSession()->set('ScheduleSearchData',json_encode($searchData));
                
                return $this->redirect($this->generateUrl('cerad_tourn_schedule_referee_list'));
            }
        }
      //print_r($searchData);
      //$games = array();
        $games = $manager->loadGames($searchData);

        $tplData = array();
        $tplData['games']      = $games;
        $tplData['isAdmin']    = false;
        $tplData['searchForm'] = $searchForm->createView();
        
        return $this->render('@CeradTourn/schedule/referee/index.html.twig', $tplData);
    }
    public function assignAction(Request $request, $id = 0, $pos = null)
    {
        $this->manager = $manager = $this->get('cerad_tourn.schedule.manager');
        
        // Grab the game
        $gameId = $id;
        $game = $manager->loadGame($gameId);
        if (!$game)
        {
            return  $this->redirect($this->generateUrl('cerad_tourn_schedule_referee_list'));
        }
        // Need officials for the account
        $user = $this->getUser();
        if (!is_object($user)) return $this->redirect($this->generateUrl('cerad_tourn_schedule_referee_list'));
        
        $officials = array();
        foreach($user->getPerson()->getPersons() as $personPerson)
        {
            $officials[] = $personPerson->getSlave();
        }
        
        // The form stuff
        $formType = $this->get('cerad_tourn.schedule.referee.assign.formtype');
        $formType->setOfficials($officials);
        $form = $this->createForm($formType, $game);
        
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);

            if ($form->isValid() | 1)
            {   
                // Disable signups
                $this->assignOfficials($manager,$game);
                
                // Should redirect
                return $this->redirect($this->generateUrl('cerad_tourn_schedule_referee_assign',array('id' => $gameId)));
            }
            else die('Form not valid');
        }
        // And do it
        $tplData = array();
        $tplData['game'] = $game;
        $tplData['form'] = $form->createView();
        return $this->render('@CeradTourn/schedule/referee/assign.html.twig',$tplData);
    }
    protected function assignOfficial($manager,$game,$gamePerson)
    {
        $personIdx = $gamePerson->getPersonx(); // Posted value
        $personId  = $gamePerson->getPerson ();
       
        // Posted a person but no existing person
        if ($personIdx && !$personId)
        {
            $person = $manager->loadPerson($personIdx);
            $gamePerson->setPerson($personIdx);
            $gamePerson->setName  ($person->getName());
            $gamePerson->setBadge ($person->getCertRefereeAYSO()->getBadge());
            $gamePerson->setLeague($person->getVolunteerAYSO()->getLeague());
            $gamePerson->setStatus('AssignmentRequested');
            return true;
        }
        // Had one but don't anymore
        if (!$personIdx && $personId)
        {
            // The form should prevent this from happening
            return false;
        }
        // Nothing before or after
        if (!$personIdx && !$personId)
        {
           $gamePerson->setStatus('Open');
           return true;
        }
        // Both have something
        if ($personIdx == $personId)
        {
            // Check state change request
            $statusx = $gamePerson->getStatusx();
            $status  = $gamePerson->getStatus();
            
            if ($statusx == 'RequestRemoval')
            {
                if ($status == 'AssignmentRequested')
                {
                    $gamePerson->setName  (null);
                    $gamePerson->setBadge (null);
                    $gamePerson->setPerson(null);
                    $gamePerson->setLeague(null);
                    $gamePerson->setStatus('Open');
                    return true;
                }
                else
                {
                    $gamePerson->setStatus($statusx);
                    return true;
                }
            }
            // Admin will use a different form
            if ($statusx == 'AssignmentApproved')
            {
                return false;
                
                if (!$this->isUserSuperAdmin()) return false;
                $gamePersonRel->setState($statex);
                $gamePersonRel->setAdminModified();
                return true;
            }
            return false;
        }
        // Both have something but are different
        // The form will prevent someone from trying to modify someone from outside their group
        $person = $manager->loadPerson($personIdx);
        
        $gamePerson->setPerson($personIdx);
        $gamePerson->setName  ($person->getName());
        $gamePerson->setBadge ($person->getCertRefereeAYSO()->getBadge());
        $gamePerson->setLeague($person->getVolunteerAYSO()->getLeague());
        $gamePerson->setStatus('AssignmentRequested');
        
        $gamePerson->setUserModified();
        
        return true;
    }
    protected function assignOfficials($manager,$game)
    {
        $gamePersons = $game->getPersons();
        foreach($gamePersons as $gamePerson)
        {
            $this->assignOfficial($manager,$game,$gamePerson);
        }
        $manager->gameManager->flush();
        return;
    }
}
?>
