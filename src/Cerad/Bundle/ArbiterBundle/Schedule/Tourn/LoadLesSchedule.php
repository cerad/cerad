<?php
namespace Cerad\Bundle\ArbiterBundle\Schedule\Tourn;

use Cerad\Component\Excel\Loader as BaseLoader;

class LoadLesSchedule extends BaseLoader
{
    protected $record = array
    (
        'num'  => array('cols' => 'Game No.','req' => true),
        'date' => array('cols' => 'Date',    'req' => true),
        'time' => array('cols' => 'Start',   'req' => true),
        
        'flight'   => array('cols' => 'Flight',  'req' => true),
        'bracket'  => array('cols' => 'Bracket', 'req' => true),
        'field'    => array('cols' => 'Field',   'req' => true),
        'type'     => array('cols' => 'Type',    'req' => true),
        
        'homeSeed' => array('cols' => 'Home Seed', 'req' => true),
        'homeClub' => array('cols' => 'Home Club', 'req' => true),
        'homeTeam' => array('cols' => 'Home Team Full', 'req' => true),
        
        'awaySeed' => array('cols' => 'Away Seed', 'req' => true),
        'awayClub' => array('cols' => 'Away Club', 'req' => true),
        'awayTeam' => array('cols' => 'Away Team Full', 'req' => true),
      
    );
    protected function processItem($item)
    {
        $num = $item['num'] + 7000;
        
        $date = $item['date'];
        $time = $item['time'];
        
        if (strlen($item['flight']) > strlen($item['bracket'])) $level = $item['flight'];
        else                                                    $level = $item['bracket'];
        
        if (substr($level,0,2) == 'U9') $level = 'U09' . substr($level,2);
        
        switch($item['type'])
        {
            case 'Group Play':  $type = 'PP'; break;
            case 'Semi-Finals': $type = 'SF'; break;
            case 'Final':       $type = 'FM'; break;
            case 'Consolation': $type = 'CM'; break;
            
            default: print_r($item); die("\nTYPE\n");
        }
        if (strlen($item['homeTeam'])) $homeTeam = $item['homeTeam'];
        else                           $homeTeam = $type . ' ' . $item['homeClub'];
        
        if (strlen($item['awayTeam'])) $awayTeam = $item['awayTeam'];
        else                           $awayTeam = $type . ' ' . $item['awayClub'];
        
        if ($homeTeam[0] == "'") $homeTeam = substr($homeTeam,1);
        if ($awayTeam[0] == "'") $awayTeam = substr($awayTeam,1);
        
        if (isset($this->sites[$item['field']])) $site = $this->sites[$item['field']];
        else
        {
            print_r($item); die("\nFIELD\n");
        }
        $game = array
        (
            'num'   => $num,
            'date'  => $date,
            'time'  => $time,
            'type'  => $type,
            'sport' => 'HFC Classic',
            'level' => $level,
            'site'  => $site,
            'home'  => $homeTeam,
            'away'  => $awayTeam,
        );
        
        $this->items[] = $game;
        
        echo sprintf("%4d %8s %8s %-16s %-20s %-40s %-40s\n",$num,$date,$time,$level,$site,$homeTeam,$awayTeam);
        
        //print_r($item); die("\n");
    }
    protected $sites = array
    (
        'Merrimack 1a Big North #M01N'   => 'Merrimack, MM01N',  
        'Merrimack 1b South Small #M01S' => 'Merrimack, MM01S',  
        'Merrimack 10 Small #M10S'       => 'Merrimack, MM10',  
        'Merrimack 10 Big #M10B'         => 'Merrimack, MM10',  
        'Merrimack 9 Big #M09B'          => 'Merrimack, MM09',  
        
        'Merrimack 1 #M01'               => 'Merrimack, MM01',  
        'Merrimack 2 #M02'               => 'Merrimack, MM02',  
        'Merrimack 3 #M03'               => 'Merrimack, MM03',  
        'Merrimack 4 #M04'               => 'Merrimack, MM04',  
        'Merrimack 5 #M05'               => 'Merrimack, MM05',  
        'Merrimack 6 #M06'               => 'Merrimack, MM06',  
        'Merrimack 7 #M07'               => 'Merrimack, MM07',  
        'Merrimack 8 #M08'               => 'Merrimack, MM08',  
        'Merrimack 9 #M09'               => 'Merrimack, MM09',  
      
        'John Hunt 1 #JH1'        => 'John Hunt, JH01',
        'John Hunt 2 #JH2'        => 'John Hunt, JH02',
        'John Hunt 3 #JH3'        => 'John Hunt, JH03',
        'John Hunt 4 #JH4'        => 'John Hunt, JH04',
        'John Hunt 5 Small #JH5S' => 'John Hunt, JH05',
        'John Hunt 6 #JH6'        => 'John Hunt, JH06',
    );
}
?>
