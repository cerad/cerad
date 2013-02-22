<?php
namespace Cerad\Bundle\GameBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/* ==============================================
 * Each game has a project and a level
 * game.num is unique within project
 */
class Game extends BaseEntity
{
    const RoleGame = 'Game';  // Practice, Scrimmage, Jamboree etc

    protected $id;
    
    protected $num;   // Unique within project
    protected $role;
    protected $pool;  // For computing standings
    protected $link;  // Maybe to link crews?
    
    protected $dtBeg; // DateTime begin
    protected $dtEnd; // DateTime end
    
    protected $league;
    protected $project;
    protected $level;
    protected $field;
    
    protected $status;   // Eventually want a few more workflow fields
    
    protected $rules;    // Game specific rules
    protected $billTo;
    
    protected $teams;    // Game teams
    protected $persons;  // Game persons (officials usually)
    
    // TODO add reporting and comments, maybe some change notification work flows
    
    public function getId()      { return $this->id;      }
    public function getNum()     { return $this->num;     }
    public function getRole()    { return $this->role;    }
    public function getPool()    { return $this->pool;    }
    public function getLink()    { return $this->link;    }
    public function getField()   { return $this->field;   }
    public function getLevel()   { return $this->level;   }
    public function getDtBeg()   { return $this->dtBeg;   }
    public function getDtEnd()   { return $this->dtEnd;   }
    public function getRules()   { return $this->rules;   }
    public function getStatus()  { return $this->status;  }
    public function getLeague()  { return $this->league;  }
    public function getProject() { return $this->project; }
    
    public function setNum    ($value) { $this->onPropertySet('num',    $value); }
    public function setLink   ($value) { $this->onPropertySet('link',   $value); }
    public function setRole   ($value) { $this->onPropertySet('role',   $value); }
    public function setPool   ($value) { $this->onPropertySet('pool',   $value); }
    public function setField  ($value) { $this->onPropertySet('field',  $value); }
    public function setLevel  ($value) { $this->onPropertySet('level',  $value); }
    public function setDtBeg  ($value) { $this->setDateTime  ('dtBeg',  $this->dtBeg, $value); }
    public function setDtEnd  ($value) { $this->setDateTime  ('dtEnd',  $this->dtEnd, $value); }
    public function setRules  ($value) { $this->onPropertySet('rules',  $value); }
    public function setStatus ($value) { $this->onPropertySet('status', $value); }
    public function setLeague ($value) { $this->onPropertySet('league', $value); }
    public function setProject($value) { $this->onPropertySet('project',$value); }
    
    /* =======================================
     * Create factory
     * Too many parameters
     */
    public function __construct()
    {
        $this->teams   = new ArrayCollection();
        $this->persons = new ArrayCollection();
    }
    static function create($params = array())
    {
        $item = new self();
        
        // Required with defaults
        if (isset($params['role'])) $item->setRole($params['role']);
        else                        $item->setRole(self::RoleGame);
        
        if (isset($params['status'])) $item->setStatus($params['status']);
        else                          $item->setStatus('Normal');
        
        // Maybe check for string and generate DT
        if (isset($params['dt_beg'])) $item->setDtBeg($params['dt_beg']);
        else                          $item->setDtBeg(new \DateTime());
        
        // Not required
        if (isset($params['num'    ])) $item->setNum($params['num'    ]);
        if (isset($params['field'  ])) $item->setNum($params['field'  ]);
        if (isset($params['level'  ])) $item->setNum($params['level'  ]);
        if (isset($params['project'])) $item->setNum($params['project']);

        return $item;
    }
    /* =======================================
     * Team stuff
     */
    public function getTeams($sort = true) 
    { 
        if (!$sort) return $this->teams;
        
        $items = $this->teams->toArray();
        
        ksort ($items);
        return $items; 
    }
    public function addTeam($team)
    {
        $this->teams[$team->getSlot()] = $team;
        
        $team->setGame($this);
    }
    public function getTeamForSlot($slot)
    {
        if (isset($this->teams[$slot])) return $this->teams[$slot];
        
        return null;
    }
    public function getHomeTeam() { return $this->getTeamForSlot(GameTeam::SlotHome); }
    public function getAwayTeam() { return $this->getTeamForSlot(GameTeam::SlotAway); }
    
    /* =======================================
     * Person stuff
     */
    public function getPersons($sort = true) 
    { 
        if (!$sort) return $this->persons;
        
        $items = $this->persons->toArray();
        
        ksort ($items);
        return $items; 
    }
    public function addPerson($person)
    {
        $this->persons[$person->getSlot()] = $person;
        
        $person->setGame($this);
    }
    public function getPersonForSlot($slot)
    {
        if (isset($this->persons[$slot])) return $this->persons[$slot];
        
        return null;
    }
    /* =========================================
     * Want to explicitly compare DT values to avoid an unnecessary property change
     */
    public function setDateTime($name,$oldValue,$newValue)
    {
        // No old value
        if (!$oldValue) return $this->onPropertySet($name,$newValue);
        
        // Just a reset
        if (!$newValue) return $this->onPropertySet($name,$newValue);
        
        // Have two dt objects
        $dtNew = $newValue->format('Y-m-d H:m:s');
        $dtOld = $oldValue->format('Y-m-d H:m:s');
        
        if ($dtNew == $dtOld) return;
        
        return $this->onPropertySet($name,$newValue);
    }
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        ob_start();

        echo sprintf("Game %-6s %-4s %6s %-8s %s   %-8s %-10s %s\n",
            $this->status,
            $this->role,
            $this->num,
            $this->project->getSeason(),
            $this->dtBeg->format('d M Y H:i:s A'),
            $this->level->getDomainSub(),
            $this->level->getName(),
            $this->field->getName()
        );
        foreach($this->teams as $team)
        {
            echo $team . "\n";
        }
        return ob_get_clean();
    }
}
?>
