<?php

namespace Zayso\NatGamesBundle\Controller\Admin\Schedule;

use Zayso\CoreBundle\Component\Debug;
use Zayso\CoreBundle\Controller\BaseController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PrintSchedController extends BaseController
{
    protected $projectId = 52;
    protected $excelTpl  = 'Admin/Schedule:print.excel.php';
    protected $fileName  = 'PrintSchedule';
    
    protected $dates = array();
    
    public function printAction(Request $request)
    {
        $manager = $this->get('zayso_core.game.schedule.manager');
        
        $searchData['projectId'] = $this->projectId;
        $searchData['dates']     = $this->dates;
        $searchData['genders']   = array('B','G');
        $searchData['orderBy']   = 'print';
        
        $games = $manager->loadGames($searchData);
        
        $tplData = array();
        $tplData['games'] = $games;
        $tplData['excel'] = $this->get('zayso_core.format.excel');
        $response = $this->renderx($this->excelTpl,$tplData);
        
        $outFileName = $this->fileName . date('YmdHi') . '.xls';
        
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', "attachment; filename=\"$outFileName\"");
        return $response;
    }
    public function soccerfestAction(Request $request)
    {
        $this->fileName  = 'SoccerfestSchedule';
        $this->dates = array('20120704');
        return $this->printAction($request);        
   }
    public function thursdayAction(Request $request)
    {
        $this->fileName  = 'ThursdaySchedule';
        $this->dates = array('20120705');
        return $this->printAction($request);        
   }
    public function fridayAction(Request $request)
    {
        $this->fileName  = 'FridaySchedule';
        $this->dates = array('20120706');
        return $this->printAction($request);        
   }
    public function saturdayAction(Request $request)
    {
        $this->fileName  = 'SaturdaySchedule';
        $this->dates = array('20120707');
        return $this->printAction($request);        
   }
    public function sundayAction(Request $request)
    {
        $this->fileName  = 'SundaySchedule';
        $this->dates = array('20120708');
        return $this->printAction($request);        
   }
}
