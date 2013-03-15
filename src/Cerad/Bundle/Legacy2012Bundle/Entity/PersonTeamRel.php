<?php
namespace Zayso\CoreBundle\Entity;

class PersonTeamRel extends BaseEntity
{   
    const TypeHeadCoach  = 'HeadCoach';
    const TypeAsstCoach  = 'AsstCoach';
    const TypeManager    = 'Manager';
    
    const TypeParent   = 'Parent';
    const TypePlayer   = 'Player';
    const TypeSpec     = 'Spectator';
    
    const TypeConflict = 'Conflict';
    const TypeBlocked  = 'Blocked'; // ByPerson, ByTeam, ByAdmin
    const TypeBlockedByPerson  = 'BlockedByPerson'; // ByPerson, ByTeam, ByAdmin

    protected $id;
    
    protected $project;
    
    protected $person = null;
    
    protected $team = null;
    
    protected $type = null;
    
    protected $priority = 0;

    protected $datax = null;

    // And this was for ???
    protected $delete = false;
    public function getDelete()        { return $this->delete; }
    public function setDelete($delete) { $this->delete = $delete; }
    
    /* =========================================================
     * Standard getter/setter
     */    
    public function getId()    { return $this->id; }
    public function setId($id) { return $this->id = $id; }
    
    public function setType($type) { $this->onScalerPropertySet('type', $type); }
    public function getType()      { return $this->type;  }
    
    public function setPriority($priority) { $this->onScalerPropertySet('priority', $priority); }
    public function getPriority()          { return $this->priority;  }
    
    public function setTeam($team) { $this->onObjectPropertySet('team', $team); }
    public function getTeam()      { return $this->team;  }
    
    public function setPerson($person) { $this->onObjectPropertySet('person', $person); }
    public function getPerson()        { return $this->person;  }
    
    public function setProject($project) { $this->onObjectPropertySet('project', $project); }
    public function getProject()         { return $this->project;  }

}
?>
