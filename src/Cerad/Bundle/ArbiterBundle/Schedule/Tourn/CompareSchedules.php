<?php
namespace Zayso\ArbiterBundle\Schedule;

use Zayso\CoreBundle\Component\Debug;

class CompareSchedules
{
    protected function gameIsDifferent($prop,$game1,$game2)
    {
        echo sprintf("Arb %s %s %s %s %s\n...%s %s\n",
                $game1->getGameNum(),$game1->getDate(),$game1->getTime(),$game1->getSite(),$game1->getLevel(),$game1->getHomeTeam(),$game1->getAwayTeam());
        
            
        echo sprintf("Les %s %s %s %s %s\n...%s %s\n",
                $game2->getGameNum(),$game2->getDate(),$game2->getTime(),$game2->getSite(),$game2->getLevel(),$game2->getHomeTeam(),$game2->getAwayTeam());
        
        echo "Property $prop\n";
        
        //Debug::dump($game1);
        //Debug::dump($game2);
        // die("Property $prop\n");
    }
    protected function compareTeams($prop,$game1,$game2)
    {
        $getTeam = 'get' . $prop;
        
        $team1 = $game1->$getTeam();
        $team2 = $game2->$getTeam();
        
        $len = strlen($team1);
        
        if ($team1 == substr($team2,0,$len)) return;
        
        //echo "'$team1' '$team2'\n";
        
        if ($team2) return $this->gameIsDifferent($prop,$game1,$game2);
        
        return;
        
        die('Team 1 ' . $team1 . "\n");
    }
    public function compareGame($game1,$game2)
    {
        if ($game1->getDate() != $game2->getDate()) $this->gameIsDifferent('Date',$game1,$game2);
        if ($game1->getTime() != $game2->getTime()) $this->gameIsDifferent('Time',$game1,$game2);
        
        // Problem with semi-finals
        $this->compareTeams('HomeTeam',$game1,$game2);
      
      //if ($game1->getHomeTeam() != $game2->getHomeTeam()) $this->gameIsDifferent('HomeTeam',$game1,$game2);
      //if ($game1->getAwayTeam() != $game2->getAwayTeam()) $this->gameIsDifferent('AwayTeam',$game1,$game2);
        
        if ($game1->getLevel() != $game2->getLevel()) $this->gameIsDifferent('Level',$game1,$game2);
        if ($game1->getSite () != $game2->getSite ()) $this->gameIsDifferent('Site', $game1,$game2);
        
    }
    public function compare($games1,$games2)
    {
        // Make sure each gets processed
        $games2x = array();
        foreach($games2 as $game) { $games2x[$game->getId()] = true; }
        
        foreach($games1 as $game1)
        {
            $id = $game1->getId();
            if (!isset($games2[$id]))
            {
                echo "No Les Game for Arbiter Game $id\n";
            }
            else
            {
                unset($games2x[$id]);
                $game2 = $games2[$id];
                
                $this->compareGame($game1,$game2);
            }
        }
        if (count($games2x))
        {
            'Les Games not found ' . implode(',',$games2x) . "\n";
        }
        echo "### Comparison done\n";
    }
}

?>
