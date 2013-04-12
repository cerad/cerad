<?php
namespace Zayso\ArbiterBundle\Schedule;

use Zayso\ArbiterBundle\Entity\Game;

class SaveRefereeSchedule
{
    protected $refereeCurrent = null;
    
    protected function writePosition($file,$referee,$pos,$game)
    {
        if (!$referee) return;
        
        if ($this->refereeCurrent != $referee)
        {
            $this->refereeCurrent = $referee;
            fputcsv($file,array());
        }
        switch($game->getDate())
        {
            case '10/19/2012': $dow = 'FRI'; break;
            case '10/20/2012': $dow = 'SAT'; break;
            case '10/21/2012': $dow = 'SUN'; break;
            default:           $dow = 'DOW';
        }
        
        $data = array
        (
            $referee,
            $pos,
            $game->getGameNum(),
            $game->getDate(),
            $dow,
            $game->getTime(),
            $game->getSport(),
            $game->getLevel(),
            '','',
            $game->getSite(),
            $game->getHomeTeam(),
            $game->getAwayTeam(),
        );
        fputcsv($file,$data);
         
        
    }
    protected $referees = array();
    
    protected function addPosition($referee,$pos,$game)
    {
        if (!$referee) return;
        
        $data = array('referee' => $referee, 'pos' => $pos,'game' => $game);
        
        if (!isset($this->referees[$referee])) $this->referees[$referee] = array();
        
        $this->referees[$referee][] = $data;
        
        return;
    }
    public function getRefereeForTeam($team)
    {
        switch($team)
        {
            case 'RIVER CITY UNITED RAPTORS':               return 'D.J. Rector';        break;
            case 'HUNTSVILLE FC HFC 00 BLUE (AL)':          return 'Mike Newchurch';     break;
            case 'HUNTSVILLE FUTBOL GIRLS 96 MAROON (AL)':  return 'James Rogers';       break;
            case 'HUNTSVILLE FC HFC 97 MAROON (AL)':        return 'Mike Futrell';       break;
            case 'RIVER CITY UNITED RC REVOLUTION (AL)':    return 'Tim Holt';           break;
            case 'RIVER CITY UNITED U11- RAPTORS (AL)':     return 'Andy Dye';           break;
            case 'AYSO 160 LADY MAVERICKS (AL), Todd Owen': return 'Todd Owen';          break;
            case 'HUNTSVILLE FUTBOL 01 MAROON BOYS (AL)':   return 'Christopher Malone'; break;
            case 'HUNTSVILLE FUTBOL 01 GIRLS (AL)':         return 'Gene Uhl';           break;
            case 'HUNTSVILLE FUTBOL 95 BOYS MAROON (AL)':   return 'Paul Galloway';      break;
            case '': return ''; break;
            case '': return ''; break;
            case '': return ''; break;
        
        }
        return null;
    }
    public function save($fileName,$games)
    {
        $file = fopen($fileName,'wt');
        
        $headers = array('Referee','Pos',
            'Game','Date','DOW','Time', 'Sport','Level','Blah','Blah',
            'Site','Home-Team', 'Away-Team'
        );
        fputcsv($file,$headers);
        
        foreach($games as $game)
        {
            $this->addPosition($game->getCR (),'CR',$game);
            $this->addPosition($game->getAR1(),'AR',$game);
            $this->addPosition($game->getAR2(),'AR',$game);
            
            $referee = $this->getRefereeForTeam($game->getHomeTeam());
            $this->addPosition($referee,'SPEC',$game);
            
            $referee = $this->getRefereeForTeam($game->getAwayTeam());
            $this->addPosition($referee,'SPEC',$game);
        }
        ksort($this->referees);
        
        foreach($this->referees as $referee)
        {
            foreach($referee as $position)
            {
                $this->writePosition($file,$position['referee'],$position['pos'],$position['game']);
            }
        }
        fclose($file);
    }
}

?>
