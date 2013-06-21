<?php
namespace Cerad\Bundle\TournBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RefereeController extends Controller
{
 
    public function refereeAction(Request $request, $_format)
    {   
        $manager = $this->get('cerad_tourn.schedule.manager');
        $project = $this->get('cerad_tourn.project');
        
        // Build the search parameter information
        $searchData = array();
        
        /* ===========================================================
         * The project key replaces all of these
        $searchData['domains']    = array($project->getDomain());
        $searchData['domainSubs'] = array($project->getDomainSub());
        $searchData['seasons']  = array($project->getSeason());
        $searchData['sports']   = array($project->getSport());
        */
        $searchData['projects'] = $project->getKeySearch();
        
        /* ===========================================================
         * Search criteria from the project file
         */
        $query    = $request->query->all();
        $searches = $project->getSearches();
        foreach($searches as $key => $search)
        {
            // Pull optional defaults if there are no query parameters
            if (!count($query))
            {
                $default = isset($search['default']) ? $search['default']: array();
                if (!is_array($default)) $default = array($default); // Form needs array
            }
            else $default = array();
            $searchData[$key] = $default;
        }
      //print_r($searchData); die();
        
      //$searchData['ages']    = array();
      //$searchData['genders'] = array();
      //$searchData['dates'] = array('2013-06-14');
        
        // My stuff
        $searchData['myTeams']   = array();
        $searchData['myPersons'] = array();
        
        // Filters
        $searchData['teamFilter']    = null;
        $searchData['refereeFilter'] = null;
        $searchData['numFilter']     = null;
        
        // Time stuff
        $searchData['time1'] = null;
        $searchData['time2'] = null;
        
        $searchData['sortBy'] = null;
        
        // Future stuff
        //$searchData['levels']   = array();
        //$searchData['teams' ]   = array();
        //$searchData['fields']   = array();
        //$searchData['statuses'] = array();
        //
        // Merge in query parameters
        $searchData = array_merge($searchData,$query);
                
        // Pull from session if nothing was passed
        if (!count($query))
        {
            $sessionSearchData = $request->getSession()->get('ScheduleSearchData');
            if ($sessionSearchData) $searchData = array_merge($searchData,json_decode($sessionSearchData,true));
        }
        // Build the form
        $searchFormType = $this->get('cerad_tourn.schedule.referee.search.formtype');
        $searchForm     = $this->createForm($searchFormType,$searchData);
        
        $searchForm->handleRequest($request);

        if ($searchForm->isValid())
        {
            $searchData = $searchForm->getData(); // print_r($searchData); die( 'POSTED');
                
            $request->getSession()->set('ScheduleSearchData',json_encode($searchData));
            
            unset($searchData['projects']);
            return $this->redirect($this->generateUrl('cerad_tourn_schedule_referee_list',$searchData));
        }

        // Must have at leaset one day explocitly set to avoid loading in everthing
        $games = array();
        if (isset($searchData['dates']) && count($searchData['dates'])) $games = $manager->loadGames($searchData);
        
        // Filter
        $games = $this->processFilters($games,$searchData['teamFilter'],$searchData['refereeFilter'],$searchData['numFilter']);
        
        // csv processing
        if ($_format == 'csv')
        {
            $export = $this->get('cerad_tourn.schedule.referee.export.csv');
            $response = new Response($export->generate($games));
        
            $outFileName = 'RefSched' . date('YmdHi') . '.csv';
        
            $response->headers->set('Content-Type',       'text/csv;');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
            return $response;
        }
        if ($_format == 'xls')
        {
            $export = $this->get('cerad_tourn.schedule.referee.export.xls');
            $response = new Response($export->generate($games));
        
            $outFileName = 'RefSched' . date('YmdHi') . '.xls';
        
            $response->headers->set('Content-Type',       'application/vnd.ms-excel');
            $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"',$outFileName));
            return $response;
        }
         
        // html processing
        $tplData = array();
        $tplData['games']      = $games;
        $tplData['isAdmin']    = false;
        $tplData['searchForm'] = $searchForm->createView();
        
        return $this->render('@CeradTourn/schedule/referee/index.html.twig', $tplData);
    }
    /* =====================================================================
     * The ever popular filter
     * Could be moved to a service as well
     */
    protected function getFilters($filter)
    {
        $filter = strtolower(trim($filter));
        if (!$filter) return null;
        
        $filters = explode(',',$filter);
        array_walk($filters,create_function('&$val', '$val = trim($val);'));
        
        return $filters;
        
    }
    protected function processFilters($games,$teamFilter,$refereeFilter,$numFilter)
    {
        // Make sure we got something
        $numFilters     = $this->getFilters($numFilter);
        $teamFilters    = $this->getFilters($teamFilter);
        $refereeFilters = $this->getFilters($refereeFilter);
        
        if (!$teamFilter && !$refereeFilter && !$numFilters) return $games;
                
        // The filteres results
        $gamesx = array();
        foreach($games as $game)
        {
            $keep = false;
            
            if ($numFilters) 
            {
                $gameNum = $game->getNum();
                foreach($numFilters as $filter)
                {
                    if ($gameNum == $filter) $keep = true;
                }
            }
            if ($teamFilters) 
            {
                foreach($game->getTeams() as $team)
                {
                    $name = strtolower($team->getName());
                    foreach($teamFilters as $filter)
                    {
                        if (strpos($name,$filter) !== false)
                        {
                            $keep = true;
                            $team->setSelected(true);
                        }
                    }
                }
            }
            if ($refereeFilters)
            {
                foreach($game->getPersons() as $person)
                {
                    $name = strtolower($person->getName());
                    if ($name)
                    {
                        foreach($refereeFilters as $filter)
                        {
                            if (strpos($name,$filter) !== false)
                            {
                                $keep = true;
                                $person->setSelected(true);
                            }
                        }
                    }
                }
            }
            if ($keep) $gamesx[] = $game;
        }
        return $gamesx;
    }
    /* =====================================================================
     * Assigning is a pain
     */
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
        
        // Global disabling
        $enabled = 
                $this->container->hasParameter('cerad_tourn_referee_signups_enabled') ? 
                $this->container->getParameter('cerad_tourn_referee_signups_enabled') :
                true;
        
        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);

            if ($form->isValid() && $enabled)
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
        $tplData['enabled'] = $enabled;
        return $this->render('@CeradTourn/schedule/referee/assign.html.twig',$tplData);
    }
    /* ============================================================
     * This all need to get moved to an assigning service of some sort
     */
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
