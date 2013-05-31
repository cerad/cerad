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
    public function myAction(Request $request)
    {   
        $tplData = array();
        return $this->render('@CeradTourn/schedule/my/index.html.twig', $tplData);
    }
}
?>
