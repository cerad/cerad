<?php
class S5GamesResultsExport
{
    protected $counts = array();
    
    protected $widths = array
    (
        'Game'      =>  6,
        'Status 1'  => 10,
        'Status 2'  => 10,
        'PA'        =>  4,
        'DOW Time'  => 15,
        'Field'     =>  6,
        'Pool'      => 12,
        'Type'      => 6,
        'Team'      => 30,
            
        'GS' => 5,
        'SP' => 5,
        'YC' => 5,
        'RC' => 5,
        'CE' => 5,
        'PE' => 5,
        
        'WPF' => 5,
        'TPE' => 5,
        'GT'  => 5,
        'GP'  => 5,
        'GW'  => 5,
        'TGS' => 5,
        'TGA' => 5,
        'TYC' => 5,
        'TRC' => 5,
        'TCE' => 5,
        'TSP' => 5,
        'SfP' => 5,
    );
    protected $center = array
    (
        'Game','HGS','HPM','HPE','APE','APM','AGS',
        'PE','PM','GP','GW','GS','GA','YC','RC','CD','SD','SP',
    );
    
    public function __construct($excel,$pools)
    {
        $this->excel = $excel;
        $this->pools = $pools;
    }
    protected function setHeaders($ws,$map,$row)
    {
        $col = 0;
        foreach(array_keys($map) as $header)
        {
            $ws->getColumnDimensionByColumn($col)->setWidth($this->widths[$header]);
            $ws->setCellValueByColumnAndRow($col++,$row,$header);
            
            if (in_array($header,$this->center))
            {
                // Works but not for multiple sheets?
              //$ws->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }
        return $row;
    }
    public function generatePoolGames($ws,$games,&$row)
    {
        $map = array(
            'Game'     => 'game',
            'Status 1' => 'status',
            'Status 2' => 'status',
            'PA'       => 'pointsApplied',
            'DOW Time' => 'date',
            'Field'    => 'field',
            'Pool'     => 'pool',
            'Type'     => true,
            'Team'     => true,
            
            'GS' => true,
            'SP' => true,
            'YC' => true,
            'RC' => true,
            'CE' => true,
            'PE' => true,
        );
        $row = $this->setHeaders($ws,$map,$row);
        
        foreach($games as $game)
        {
            
            $row++;
            $col = 0;
           
            $homeTeam   = $game->getHomeTeam();
            $awayTeam   = $game->getAwayTeam();
            $homeReport = $homeTeam->getReport();
            $awayReport = $awayTeam->getReport();
            
            // Page break on pool change
            $poolx = null;
            $pool = $game->getPool();
            if ($poolx != $pool)
            {
                 //if ($poolx) $ws->setBreak('A' . $row, \PHPExcel_Worksheet::BREAK_ROW);
            }
            $poolx = $pool;
            
            $date = $game->getDate();
            $time = $game->getTime();
            
            $stamp = mktime(0,0,0,substr($date,4,2),substr($date,6,2),substr($date,0,4));
            $date = date('D',$stamp);
            
            $stamp = mktime(substr($time,0,2),substr($time,2,2));
            $time = date('h:i A',$stamp);
            
            $dtg = $date . ' ' . $time;
          
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getStatus());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getReportStatus());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getPointsApplied());
            $ws->setCellValueByColumnAndRow($col++,$row,$dtg);
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getFieldDesc());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getPool());
            
            $awayFlag = false;
            foreach(array($homeTeam,$awayTeam) as $team)
            {
                $report = $team->getReport();
                
                if ($awayFlag)
                {
                    $row++;
                    $ws->setCellValueByColumnAndRow(0,$row,$game->getNum());
                    $col = 7;
                }
                if ($awayFlag) $ws->setCellValueByColumnAndRow($col++,$row,'Away');
                else           $ws->setCellValueByColumnAndRow($col++,$row,'Home');
                
                $ws->setCellValueByColumnAndRow($col++,$row,$team->getTeam()->getDesc());
            
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored());
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getSportsmanship());
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getCautions());
            
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getSendoffs());
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getCoachTossed());
                
                $ws->setCellValueByColumnAndRow($col++,$row,$report->getPointsEarned());
 
                $awayFlag = true;
            }
            
        }
        return;
    }
    public function generatePoolTeams($ws,$teams,&$row)
    {
        $map = array
        (
            'Pool'     => 'pool',
            'Team'     => 'team',
            
            'WPF' => true,
            'TPE' => true,
            'GT'  => true,
            'GP'  => true,
            'GW'  => true,
            'TGS' => true,
            'TGA' => true,
            'TYC' => true,
            'TRC' => true,
            'TCE' => true,
            'SfP' => true,
            'TSP' => true,
        );
        $row = $this->setHeaders($ws,$map,$row);
        
        foreach($teams as $team)
        {
            $row++;
            $col = 0;
           
            $report = $team->getReport();    
           
            $ws->setCellValueByColumnAndRow($col++,$row,$team->getKey());
            $ws->setCellValueByColumnAndRow($col++,$row,$team->getDesc());
            
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getWinPercent());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getPointsEarned());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGamesTotal());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGamesPlayed());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGamesWon());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsScored());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getGoalsAllowed());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getCautions());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getSendoffs());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getCoachTossed());
            $ws->setCellValueByColumnAndRow($col++,$row,$team->getSfSP());
            $ws->setCellValueByColumnAndRow($col++,$row,$report->getSportsmanship());
        }
        return;
    }
    protected function pageSetup($ws,$ftoh = 0)
    {
        $ws->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $ws->getPageSetup()->setPaperSize  (\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $ws->getPageSetup()->setFitToPage(true);
        $ws->getPageSetup()->setFitToWidth(1);
        $ws->getPageSetup()->setFitToHeight($ftoh);
        $ws->setPrintGridLines(true);
        return;
    }
    public function generate()
    {
        // Spreadsheet
        $ss = $this->excel->newSpreadSheet();  
        
        $i = 0;
 
        // One sheet with all for printing
        $gameWS = $ss->createSheet($i++);
        $this->pageSetup($gameWS);
        $gameWS->setTitle('Games ' . 'ALL');
        $gameRow = 1;
        
        $teamWS = $ss->createSheet($i++);
        $this->pageSetup($teamWS);
        $teamWS->setTitle('Teams ' . 'ALL');
        $teamRow = 1;

        $keyx = null;
        foreach($this->pools as $key => $pool)
        {
            if (count($pool['teams'])) {
                
            if (substr($keyx,0,7) == substr($key,0,7))
            {
                $gameRow += 2;           
                $teamRow += 1;           
            }
            else
            {
                if ($keyx) 
                {
                    $gameWS->setBreak('A' . $gameRow, \PHPExcel_Worksheet::BREAK_ROW);
                    $teamWS->setBreak('A' . $teamRow, \PHPExcel_Worksheet::BREAK_ROW);
                    $gameRow += 1;           
                    $teamRow += 1;           
                }
            }
            $this->generatePoolGames($gameWS,$pool['games'],$gameRow);               
            $this->generatePoolTeams($teamWS,$pool['teams'],$teamRow);               
            $keyx = $key;
        }}
        
        // Individual sheets
        $keyx = null;
        foreach($this->pools as $key => $pool)
        {
            if (count($pool['teams'])) {
                
            if (substr($keyx,0,7) == substr($key,0,7))
            {           
                $gameRow += 2;                         
                $teamRow += 3;           
            }
            else
            {
                $gameWS = $ss->createSheet($i++);
                $this->pageSetup($gameWS);
                $gameWS->setTitle('Games ' . substr($key,0,7));
                $gameRow = 1;
                
                $teamWS = $ss->createSheet($i++);
                $this->pageSetup($teamWS,1);
                $teamWS->setTitle('Teams ' . substr($key,0,7));
                $teamRow = 1;
            }
            // Gets called for once for A B C D etc
            
            if ($gameRow != 1) $gameWS->setBreak('A' . ($gameRow - 1), \PHPExcel_Worksheet::BREAK_ROW);
             
            $this->generatePoolGames($gameWS,$pool['games'],$gameRow);   
            
            
            $this->generatePoolTeams($teamWS,$pool['teams'],$teamRow);               
            $keyx = $key;
        }}

         
        // Output
        $ss->setActiveSheetIndex(0);
        $objWriter = $this->excel->newWriter($ss); // \PHPExcel_IOFactory::createWriter($ss, 'Excel5');

        ob_start();
        $objWriter->save('php://output'); // Instead of file name
        return ob_get_clean();
        
    }
}
$export = new S5GamesResultsExport($excel,$pools);

echo $export->generate();
 
?>
