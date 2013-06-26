<?php
namespace Cerad\Bundle\GameV2Bundle\Controller\Schedule;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MasterController extends Controller
{
    public function listAction(Request $request, $_format = 'html')
    {
        
        // Build the search parameter information
        $searchData = array();
        
        // Fixed
      //$searchData['date1'] = '2013-01-01 00:00:00';
      //$searchData['date2'] = '2013-02-15 00:00:00';
        
        $searchData['date1On'] = false;
        $searchData['date2On'] = false;
        
        $searchData['date1After' ] = false;
        $searchData['date2Before'] = false;
        
        $searchData['password'] = null;
        
        // Dynamic
        $searchData['projects']   = array();
        $searchData['sources' ]   = array();
        $searchData['sports'  ]   = array();
        $searchData['seasons' ]   = array();
        $searchData['domains']    = array();
        $searchData['domainSubs'] = array();
       
        $query = $request->query->all();
        $searchData = array_merge($searchData,$query);
        
        // Pull from session if nothing was passed
        if (!count($query))
        {
            $sessionSearchData = $request->getSession()->get('ScheduleMasterSearchData');
          //if ($sessionSearchData) $searchData = array_merge($searchData,json_decode($sessionSearchData,true));
        }
        
        // Form processing
        $searchFormType = $this->get('cerad_gamev2.schedule.master.search.formtype');
        $searchForm     = $this->createForm($searchFormType,$searchData);
        $searchForm->handleRequest($request);
        
        // Can't see to get multi select to work with empty data
        // Hack to remove null values
        $searchData = $searchForm->getData();
        foreach(array('projects','seasons','domains') as $key)
        {
            $searchData[$key] = $this->removeNulls($searchData[$key]);
        }
        
        // Return to usual processing
        if ($searchForm->isValid())
        {
            // $searchData = $searchForm->getData();
            
            $request->getSession()->set('ScheduleMasterSearchData',json_encode($searchData));
                    
            return $this->redirect($this->generateUrl('cerad_gamev2_schedule_master_list',$searchData));
        }

        // Load and filter the games
        $manager = $this->get('cerad_gamev2.game.manager');
        
        $games = $manager->loadGames($searchData,100);
        
        // html processing
        $tplData = array();
        $tplData['games']      = $games;
        $tplData['searchForm'] = $searchForm->createView();
        
        return $this->render('@CeradGameV2/schedule/master/index.html.twig', $tplData);
    }
    protected function removeNulls($values)
    {
        $valuesx = array();
        foreach($values as $value)
        {
            if ($value) $valuesx[] = $value;
        }
        return $valuesx;
    }
}
?>
