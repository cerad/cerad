<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Collections\ArrayCollection;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityPrimary as CommonBaseEntityPrimary;

class Team extends CommonBaseEntityPrimary
{
    const RolePhysical    = 'Physical';
    const RolePool        = 'Pool';
    const RolePlaceHolder = 'Placeholder';
    
    protected $role = self::RolePhysical;
    
    protected $level;  // Belongs to one and only one level
    protected $league; // Belongs to at most one league
    protected $status = 'Active';
    
    protected $teamTeams1;
    protected $teamTeams2;
    protected $teamProjects; // A team can cross between multiple projects?
    
    public function getRole  () { return $this->role;    }
    public function getLevel () { return $this->level;   }
    public function getLeague() { return $this->league;  }
    public function getStatus() { return $this->status;  }
    
    public function getTeamTeams1  () { return $this->teamTeams1;   }
    public function getTeamTeams2  () { return $this->teamTeams2;   }
    public function getTeamProjects() { return $this->teamProjects; }
   
    public function setRole   ($value) { $this->onPropertySet('role',   $value); }
    public function setLevel  ($value) { $this->onPropertySet('level',  $value); }
    public function setLeague ($value) { $this->onPropertySet('league', $value); }
    public function setStatus ($value) { $this->onPropertySet('status', $value); }
    
   /* =========================================================
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->teamTeams1    = new ArrayCollection();
        $this->teamTeams2    = new ArrayCollection();
        $this->teamProjects  = new ArrayCollection();
    }
    public function newIdentifier() { return new TeamIdentifier(); }
    

    /* =========================================
     * See how this goes
     * Only implementing master => slave for now
     * AKA addTeamSlave or addTeamRight
     */
    public function addProject(Project $item,$role = null)
    {
        // Works
        return $this->addRelItem('teamProjects',__NAMESPACE__.'\\ProjectTeam',$item,$role);
    }
    public function addTeam1(Team $item,$role = null)
    {
        // Works
        return $this->addRelItem('teamTeams1',__NAMESPACE__.'\\TeamTeam',$item,$role);
    }
    public function addTeam2(Team $item,$role = null)
    {
        // Works
        return $this->addRelItem('teamTeams2',__NAMESPACE__.'\\TeamTeam',$item,$role);
        
        $team1 = $this;
       
        // Check for dups
        foreach($this->teamTeams2 as $teamTeam)
        {
            // Roles and id's
            if (($teamTeam->getRole() == $role) && ($teamTeam->getEntity2()->getId() == $team2->getId())) return $teamTeam;
        }
        $teamTeam = new TeamTeam();
        $teamTeam->setRole   ($role);
        $teamTeam->setEntity1($team1);
        $teamTeam->setEntity2($team2);
        $teamTeam->setName1  ($team1->getName());
        $teamTeam->setName2  ($team2->getName());
        
        $this->teamTeams2[] = $teamTeam;
        
        $this->onPropertyChanged('teamTeams2');
        
        return $teamTeam;
    }
    
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
