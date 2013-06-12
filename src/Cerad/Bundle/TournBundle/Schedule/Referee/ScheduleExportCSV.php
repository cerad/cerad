<?php
namespace Cerad\Bundle\TournBundle\Schedule\Referee;

/* ============================================
 * Basic referee schedule exporter
 */
class ScheduleExportCSV
{
    public function generate($games)
    {
        $fp = fopen('php://temp','r+');

        // Header
        $row = array(
            "Game","Date","DOW","Time","Field",
            "Pool","Home Team","Away Team",
            "Referee","Asst Referee 1","Asst Referee 2",
        );
        fputcsv($fp,$row);

        // Games is passed in
        foreach($games as $game)
        {
            // Date/Time
            $dt   = $game->getDtBeg();
            $dow  = $dt->format('D');
            $date = $dt->format('M d');
            $time = $dt->format('g:i A');
            
            // Build up row
            $row = array();
            $row[] = $game->getNum();
            $row[] = $date;
            $row[] = $dow;
            $row[] = $time;
            $row[] = $game->getField()->getName();
    
            $row[] = $game->getPool() . ' ' . $game->getLevel()->getName();
            $row[] = $game->getHomeTeam()->getName();
            $row[] = $game->getAwayTeam()->getName();
    
            foreach($game->getPersons() as $gamePerson)
            {
                $row[] = $gamePerson->getName();
            }
            fputcsv($fp,$row);
        }
        // Return the content
        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        //echo $csv; //die();
        return $csv;
    }
}
?>
