<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityPrimary as CommonBaseEntityPrimary;

class Team extends CommonBaseEntityPrimary
{
    protected $name;
    protected $level;  // Belongs to one and only one level
    protected $league; // Belongs to at most one league
    protected $status = 'Active';
    
    protected $teamTeams;
    protected $projectTeams; // A team can cross between multiple projects?
    
    public function getName  () { return $this->name;    }
    public function getLevel () { return $this->level;   }
    public function getLeague() { return $this->league;  }
    public function getStatus() { return $this->status;  }
    
    public function getTeamTeams   () { return $this->teamTeams;    }
    public function getProjectTeams() { return $this->projectTeams; }
   
    public function setName   ($value) { $this->onPropertySet('name',   $value); }
    public function setLevel  ($value) { $this->onPropertySet('level',  $value); }
    public function setLeague ($value) { $this->onPropertySet('league', $value); }
    public function setStatus ($value) { $this->onPropertySet('status', $value); }
    
   /* =========================================================
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->teamTeams     = new ArrayCollection();
        $this->projectTeams  = new ArrayCollection();
    }
    public function newIdentifier() { return new TeamIdentifier(); }
    
    /*
    public function addIdentifier(LevelIdentifier $identifier)
    {
        $this->identifiers[] = $identifier;
        $identifier->setLevel($this);
        $this->onPropertyChanged('identifiers');
    }*/
    
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Team %s %s %s\n",
            $this->status,
            $this->name,
            $this->id);
    }
}
?>
