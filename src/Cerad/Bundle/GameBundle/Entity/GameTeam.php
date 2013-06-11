<?php

namespace Cerad\Bundle\GameBundle\Entity;

/* ==============================================
 * Each game_team belongs to one and only one game
 * Games never cross project boundaries so no need for a project here
 * However the teams for games can easily cross level boundaries
 * Hence, each game_team has it's own level
 */
class GameTeam extends BaseEntity
{
    const RoleHome = 'Home';
    const RoleAway = 'Away';
    
    const SlotHome = 1;
    const SlotAway = 2;

    protected $id;
    
    protected $slot;
    protected $role;
    
    protected $game;
    
    protected $team;  // Optional and will often not be used
    protected $name;  // Keep this in sync with the related team object
    
    protected $league;
    protected $level;
    
    protected $score;
    protected $conduct;  // Misconduct etc, sendoff caution sportsmanship
    
    protected $status;   // Really need? Maybe for workflow
    protected $report;
    
    public function getId()      { return $this->id;      }
    public function getSlot()    { return $this->slot;    }
    public function getRole()    { return $this->role;}
    public function getGame()    { return $this->game;    }
    public function getTeam()    { return $this->team;    }
    public function getName()    { return $this->name;    }
    public function getLevel()   { return $this->level;   }
    public function getScore()   { return $this->score;   }
    public function getStatus()  { return $this->status;  }
    public function getLeague()  { return $this->league;  }
    public function getConduct() { return $this->conduct; }
    
    public function setSlot    ($value) { $this->onPropertySet('slot',    $value); }
    public function setRole    ($value) { $this->onPropertySet('role',    $value); }
    public function setGame    ($value) { $this->onPropertySet('game',    $value); }
    public function setTeam    ($value) { $this->onPropertySet('team',    $value); }
    public function setName    ($value) { $this->onPropertySet('name',    $value); }
    public function setLevel   ($value) { $this->onPropertySet('level',   $value); }
    public function setScore   ($value) { $this->onPropertySet('score',   $value); }
    public function setStatus  ($value) { $this->onPropertySet('status',  $value); }
    public function setLeague  ($value) { $this->onPropertySet('league',  $value); }
    public function setConduct ($value) { $this->onPropertySet('conduct', $value); }
    
    /* =======================================
     * Create factory
     * Too many parameters
     */
    static function create($params = array())
    {
        $item = new self();
        
        // Required
        $item->setRole($params['role']);
        $item->setSlot($params['slot']);
        
        // Required with defaults
        if (isset($params['status'])) $item->setStatus($params['status']);
        else                          $item->setStatus('Normal');

        // Optional
        if (isset($params['name'])) $item->setName($params['name']);
        
        // Relations
        if (isset($params['game'])) $params['game']->addTeam($item);
        
        return $item;
    }
    /* =========================================
     * Game report information is stored in the conduct array
     */
    public function setReport($report)
    {
        $this->onPropertySet('report',$report);
        
        if ($report) $report->setTeam($this);
        
        return $this;
    }
    public function getReport()
    {
        if (!$this->report)
        {
            // Default status, setting takes care of the linking
            $report = new GameTeamReport();
            $this->setReport($report);
        }
        return $this->report;
    }
    /* =========================================
     * Used to highlite objects
     */
    protected $selected;
    public function getSelected()       { return $this->selected; }
    public function setSelected($value) { $this->selected = $value; return $this; }
    
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("GameTeam %-4s %8s %-8s %s",
            $this->role,
            $this->level->getDomainSub(),
            $this->level->getName(),
            $this->name
        );
    }
}
?>
