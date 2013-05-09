<?php
namespace Cerad\Bundle\TournBundle\Schedule;

use Cerad\Component\Excel\Loader as BaseLoader;

class ScheduleImport extends BaseLoader
{
    protected $record = array
    (
        'num'      => array('cols' => 'Num',  'req' => true),
        'dow'      => array('cols' => 'DOW',  'req' => true),
        'time'     => array('cols' => 'Time', 'req' => true),
        
        'level'    => array('cols' => 'Level',   'req' => true),
        'field'    => array('cols' => 'Field',   'req' => true),
        'type'     => array('cols' => 'Type',    'req' => true),
        
        'homeTeam' => array('cols' => 'Home Team', 'req' => true),
        'awayTeam' => array('cols' => 'Away Team', 'req' => true),
    );
    protected $params;
    
    public function __construct($manager)
    {
        parent::__construct();
        $this->manager        = $manager;
        $this->gameManager    = $manager->gameManager;
        $this->fieldManager   = $manager->fieldManager;
        $this->levelManager   = $manager->levelManager;
        $this->projectManager = $manager->projectManager;
    }
    protected function processItem($item)
    {
        $project = $this->project;
        
        $num = (integer)$item['num'];
        if (!$num) return;
        
        switch($item['dow'])
        {
            case 'FRI': $date = '05-17-2013'; break;
            case 'SAT': $date = '05-18-2013'; break;
            case 'SUN': $date = '05-19-2013'; break;
            default:
                print_r($item); die('*** DOW ***');
        }
        $time = $this->processTime($item['time']);

        $dtBeg = \DateTime::createFromFormat('m-d-Y*H:i:s',$date . ' ' . $time);

        $dt = $dtBeg->format('Y-m-d H:i');
       
        // Level processing
        $sex =   substr($item['level'],2,1);
        $age = (integer)$item['level'];
        
        switch($age)
        {
            case 10: $duration = 50; break;
            case 12: $duration = 60; break;
            case 14: $duration = 70; break;
            default:
                print_r($item); die('*** AGE ***');
        }
        $params = array
        (
            'sport'     => $project->getSport(),
            'domain'    => $project->getDomain(),
            'domainSub' => $project->getDomainSub(),
            'name'      => $item['level'],
            'age'       => $age,
            'sex'       => $sex,
        );
        $level = $this->levelManager->processEntity($params,$this->persistFlag);
        
        // Field processing
        $params = array
        (
            'season'    => $project->getSeason(),
            'domain'    => $project->getDomain(),
            'domainSub' => $project->getDomainSub(),
            'name'      => $item['field'],
            'venue'     => 'RR',
            'venueSub'  => substr($item['field'],2),
        );
        $field = $this->fieldManager->processEntity($params,$this->persistFlag);

        $pool = $item['type'];
        if (substr($pool,0,4) == 'POOL') $pool = substr($pool,0,4);
        
        /* ==========================================================
         * Create game unless have one
         */
        $gameManager = $this->gameManager;
        $game = $gameManager->loadGameForProjectNum($project,$num);
        if (!$game)
        {   
            $game = $gameManager->createGame();
            $gameManager->persist($game);
            
            $homeTeam = $gameManager->createGameTeamHome();
            $awayTeam = $gameManager->createGameTeamAway();
            
            $game->addTeam($homeTeam);
            $game->addTeam($awayTeam);
            
            $gameManager->createGamePerson(array('game' => $game, 'status'=> 'Open', 'slot' => 1, 'role'=> 'Referee'));
            $gameManager->createGamePerson(array('game' => $game, 'status'=> 'Open', 'slot' => 2, 'role'=> 'AR1'));
            $gameManager->createGamePerson(array('game' => $game, 'status'=> 'Open', 'slot' => 3, 'role'=> 'AR2'));
            
        }
        else
        {
            $homeTeam = $game->getHomeTeam();
            $awayTeam = $game->getAwayTeam();            
        }
        // Could do an array thing here
        $game->setNum    ($num);
        $game->setProject($project);
        $game->setLevel  ($level);
        $game->setField  ($field);
        $game->setPool   ($pool);
        
        $game->setDtBeg($dtBeg);
        $game->setDtEnd($dtBeg);
        
        $homeTeam->setLevel($level);
        $awayTeam->setLevel($level);
        
        $homeTeam->setName(strtoupper($item['homeTeam']));
        $awayTeam->setName(strtoupper($item['awayTeam']));
        
        $gameManager->flush();
        
        return;
        
        echo sprintf("DT %s %s %d %s\n",$dt,$item['dow'],$duration,$level->getName());
        return;
        print_r($item); die();
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
    public function importFile($params)
    {   
        $this->persistFlag = true;
        
        // All games belong to one project, season,sport,domain,domainSub
        $this->project = $this->projectManager->processEntity($params,$this->persistFlag);
        
        // The file
        $inputFileName = $params['inputFileName'];
        if (isset($params['worksheetName'])) $worksheetName = $params['worksheetName'];
        else                                 $worksheetName = null;
        
        $results = $this->load($inputFileName,$worksheetName);
        
        return $results;
        
    }
}
?>
