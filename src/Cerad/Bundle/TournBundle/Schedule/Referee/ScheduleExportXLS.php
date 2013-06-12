<?php
namespace Cerad\Bundle\TournBundle\Schedule\Referee;

/* ============================================
 * Basic referee schedule exporter
 */
class ScheduleExportXLS
{
    protected $counts = array();
    
    protected $widths = array
    (
        'Game' =>  6, 'Game#' =>  6,

        'DOW' =>  5, 'Date' =>  10, 'Time' => 10,
        
        'Venue' =>  8, 'Field' =>  6, 'Pool' => 12,
            
        'Home Team' => 26, 'Away Team' => 26,
        
        'Referee' => 26, 'AR1' => 26, 'AR2' => 26,
    );
    protected $center = array
    (
        'Game',
    );
    public function __construct($excel)
    {
        $this->excel = $excel;
    }
    protected function setHeaders($ws,$map,$row)
    {
        $col = 0;
        foreach(array_keys($map) as $header)
        {
            $ws->getColumnDimensionByColumn($col)->setWidth($this->widths[$header]);
            $ws->setCellValueByColumnAndRow($col++,$row,$header);
            
            if (in_array($header,$this->center) == true)
            {
                // Works but not for multiple sheets?
                // $ws->getStyle($col)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
        }
        return $row;
    }
    protected function setRow($ws,$map,$person,&$row)
    {
        $row++;
        $col = 0;
        foreach($map as $propName)
        {
            $ws->setCellValueByColumnAndRow($col++,$row,$person[$propName]);
        }
        return $row;
    }
    public function generateGames($ws,$games)
    {
        $map = array(
            'Game'     => 'game',
            'Date'     => 'date',
            'DOW'      => 'dow',
            'Time'     => 'time',
            'Venue'    => 'venue',
            'Field'    => 'field',
            'Pool'     => 'pool',
            
            'Home Team' => 'homeTeam',
            'Away Team' => 'awayTeam',
            
            'Referee' => 'referee',
            'AR1'     => 'AR1',
            'AR2'     => 'AR2',
            
            'Game#'   => 'game',
        );
        $ws->setTitle('Games');
        $row = 1;
        $row = $this->setHeaders($ws,$map,$row);
        
        $timex = null;
        
        foreach($games as $game)
        {   
            $row++;
            $col = 0;
            
            // Date/Time
            $dt   = $game->getDtBeg();
            $dow  = $dt->format('D');
            $date = $dt->format('M d');
            $time = $dt->format('g:i A');
           
            // Skip on time changes
            if ($timex != $time)
            {
                if ($timex) $row++;
                $timex = $time;
            }
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());
            $ws->setCellValueByColumnAndRow($col++,$row,$date);
            $ws->setCellValueByColumnAndRow($col++,$row,$dow);
            $ws->setCellValueByColumnAndRow($col++,$row,$time);
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getField()->getVenue());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getField()->getName ());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getPool() . ' ' . $game->getLevel()->getName());
            
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getHomeTeam()->getName());
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getAwayTeam()->getName());
            
            foreach($game->getPersons() as $gamePerson)
            {
                $ws->setCellValueByColumnAndRow($col++,$row,$gamePerson->getName());
            }
            $ws->setCellValueByColumnAndRow($col++,$row,$game->getNum());
        }
        return;
    }
    public function generate($games)
    {
        // Spreadsheet
        $ss = $this->excel->newSpreadSheet(); 
        $ws = $ss->getSheet(0);
        
        $ws->getPageSetup()->setOrientation(\PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        $ws->getPageSetup()->setPaperSize  (\PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
        $ws->getPageSetup()->setFitToPage(true);
        $ws->getPageSetup()->setFitToWidth(1);
        $ws->getPageSetup()->setFitToHeight(0);
        $ws->setPrintGridLines(true);
        
        $this->generateGames($ws,$games);
        
        // Output
        $ss->setActiveSheetIndex(0);
        $objWriter = $this->excel->newWriter($ss); // \PHPExcel_IOFactory::createWriter($ss, 'Excel5');

        ob_start();
        $objWriter->save('php://output'); // Instead of file name
        return ob_get_clean();
    }
}
?>
