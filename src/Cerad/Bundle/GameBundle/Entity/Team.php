<?php

namespace Cerad\GameBundle\Entity;

/* ==============================================
 * Project Level Names are unique
 * 
 * *** Not yet being used, Acutally just a copy of the GameTeam object
 */
class Team extends BaseEntity
{
    const RolePhysical = 'Physical';
    const RolePool     = 'Pool';
    const RolePlayoff  = 'Playoff';

    protected $id;
    
    protected $league;
    protected $project;
    protected $level;
    protected $name;
    
    protected $role;
    protected $roleSort; // Maybe slots?

    protected $link;   // Across sub domains
    protected $parent; // Pool Team can point to Physical Team
    protected $status;
    
    protected $hash; // For importing? 
    
    protected $gameTeams; // Maybe for schedule by team?
    
    public function getId()      { return $this->id;      }
    public function getRole()    { return $this->role;    }
    public function getRoleSort(){ return $this->roleSort;}
    public function getGame()    { return $this->game;    }
    public function getTeam()    { return $this->team;    }
    public function getName()    { return $this->name;    }
    public function getLevel()   { return $this->level;   }
    public function getScore()   { return $this->score;   }
    public function getStatus()  { return $this->status;  }
    public function getLeague()  { return $this->league;  }
    public function getProject() { return $this->project; }
    public function getConduct() { return $this->conduct; }
    
    public function setRole    ($value) { $this->onPropertySet('role',    $value); }
    public function setRoleSort($value) { $this->onPropertySet('roleSort',$value); }
    public function setGame    ($value) { $this->onPropertySet('game',    $value); }
    public function setTeam    ($value) { $this->onPropertySet('team',    $value); }
    public function setName    ($value) { $this->onPropertySet('name',    $value); }
    public function setLevel   ($value) { $this->onPropertySet('level',   $value); }
    public function setScore   ($value) { $this->onPropertySet('score',   $value); }
    public function setStatus  ($value) { $this->onPropertySet('status',  $value); }
    public function setLeague  ($value) { $this->onPropertySet('league',  $value); }
    public function setProject ($value) { $this->onPropertySet('project', $value); }
    public function setConduct ($value) { $this->onPropertySet('conduct', $value); }
    
    /* =======================================
     * Create factory
     */
    static function create($project,$level,$field,$role = self::RoleGame,$status = 'Normal')
    {
        $item = new self();
        
        $item->setProject($project);
        $item->setLevel  ($level);
        $item->setField  ($field);
        $item->setRole   ($role);
        $item->setStatus ($status);

        return $item;
    }
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("V4GameTeam %-4s %-6s %8s %-8s %s",
            $this->role,
            $this->project->getSeason(),
            $this->level->getDomainSub(),
            $this->level->getLevel(),
            $this->name
        );
    }
}
?>
