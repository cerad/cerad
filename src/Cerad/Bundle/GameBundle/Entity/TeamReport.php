<?php
namespace Cerad\Bundle\GameBundle\Entity;

/* =============================================================
 * For now, this entity is never persisted
 * Individual game teams will store the report data as an array
 * 
 * Also used for pool teams for summary information
 */
class TeamReport extends GameTeamReport
{  
    protected $gamesWon;
    protected $gamesTotal;
    protected $gamesPlayed;
    protected $winPercent;
    
    protected $goalsScoredMax;  // Limit to 3 per game
    protected $goalsAllowedMax; // Limit to 5 per game
    
    // Name should actually be a link to a pool team
    protected $teamName; 
    public function getTeamName()       { return $this->teamName; }
    public function setTeamName($value) { return $this->onPropertySet('teamName',$value); }
    
    public function setGamesPlayed($value) { return $this->onPropertySet('gamesPlayed',$value); }
    public function addGamesPlayed($value) { return $this->onPropertyAdd('gamesPlayed',$value); }
    public function getGamesPlayed()       { return $this->gamesPlayed; }
    
    public function setGamesWon($value)    { return $this->onPropertySet('gamesWon',$value); }
    public function addGamesWon($value)    { return $this->onPropertyAdd('gamesWon',$value); }
    public function getGamesWon()          { return $this->gamesWon; }

    public function setGamesTotal($value)  { return $this->onPropertySet('gamesTotal',$value); }
    public function addGamesTotal($value)  { return $this->onPropertyAdd('gamesTotal',$value); }
    public function getGamesTotal()        { return $this->gamesTotal; }

    public function setWinPercent($value)  { return $this->onPropertySet('winPercent',$value); }
    public function addWinPercent($value)  { return $this->onPropertyAdd('winPercent',$value); }
    public function getWinPercent()        { return $this->winPercent; }
    
    public function setGoalsScoredMax($value)  { return $this->onPropertySet('goalsScoredMax',$value); }
    public function addGoalsScoredMax($value)  { return $this->onPropertyAdd('goalsScoredMax',$value); }
    public function getGoalsScoredMax()        { return $this->goalsScoredMax; }
    
    public function setGoalsAllowedMax($value)  { return $this->onPropertySet('goalsAllowedMax',$value); }
    public function addGoalsAllowedMax($value)  { return $this->onPropertyAdd('goalsAllowedMax',$value); }
    public function getGoalsAllowedMax()        { return $this->goalsAllowedMax; }

    public function clear()     
    {
        parent::clear();
        $props = array('gamesPlayed','gamesWon','gamesTotal','winPercent','goalsScoredMax','goalsAllowedMax');
        foreach($props as $prop)
        {
            $this->onPropertySet($prop,null);
        }
        return $this;        
    }
    
    /* =================================================================
     * Inherited
     */
    public function addGoalsScored    ($value)  { return $this->onPropertyAdd('goalsScored',    $value);  }
    public function addGoalsAllowed   ($value)  { return $this->onPropertyAdd('goalsAllowed',   $value);  }
    public function addPointsEarned   ($value)  { return $this->onPropertyAdd('pointsEarned',   $value);  }
    public function addPointsMinus    ($value)  { return $this->onPropertyAdd('pointsMinus',    $value);  }
    
    public function addSportsmanship  ($value)  { return $this->onPropertyAdd('sportsmanship',  $value);  }
    public function addFudgeFactor    ($value)  { return $this->onPropertyAdd('fudgeFactor',    $value);  }
    
    public function addPlayerWarnings ($value)  { return $this->onPropertyAdd('playerWarnings', $value);  }
    public function addPlayerEjections($value)  { return $this->onPropertyAdd('playerEjections',$value);  }
    public function addCoachWarnings  ($value)  { return $this->onPropertyAdd('coachWarnings',  $value);  }
    public function addCoachEjections ($value)  { return $this->onPropertyAdd('coachEjections', $value);  }
    public function addBenchWarnings  ($value)  { return $this->onPropertyAdd('benchWarnings',  $value);  }
    public function addBenchEjections ($value)  { return $this->onPropertyAdd('benchEjections', $value);  }
    public function addSpecWarnings   ($value)  { return $this->onPropertyAdd('specWarnings',   $value);  }
    public function addSpecEjections  ($value)  { return $this->onPropertyAdd('specEjections',  $value);  }
    
    // Handy to have
    protected function onPropertyAdd($name,$value)
    {
        $value += $this->$name;
        $this->onPropertySet($name,$value);
    } 
}
?>
