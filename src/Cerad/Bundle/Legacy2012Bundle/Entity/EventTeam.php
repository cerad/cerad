<?php
namespace Zayso\CoreBundle\Entity;

class EventTeam extends BaseEntity
{   
    const TypeHome = 'Home';
    const TypeAway = 'Away';
    
    protected $id;
    
    protected $project = null;
    
    protected $event = null;
    
    protected $team = null;
    
    protected $type = null;

    protected $datax = null;

    /* =========================================================
     * Custom code
     */
    
    /* =========================================================
     * Standard getter/setter
     */    
    public function getId() { return $this->id; }
    
    public function setProject($project) { $this->onObjectPropertySet('project', $project); }
    public function getProject()         { return $this->project;     }
    
    public function setGame($event) { $this->setEvent($event); }
    public function getGame()       { return $this->event;     }
    
    public function setEvent($event) { $this->onObjectPropertySet('event', $event); }
    public function getEvent()       { return $this->event;  }
    
    public function setTypeAsHome() { $this->setType(self::TypeHome); }
    public function setTypeAsAway() { $this->setType(self::TypeAway); }
    
    public function setType($type) { $this->onScalerPropertySet('type', $type); }
    public function getType()      { return $this->type;  }
    
    public function setTeam($team) { $this->onObjectPropertySet('team', $team); }
    public function getTeam()      { return $this->team;  }

    public function getTeamKey()
    {
        if (!$this->team) return null;
        return $this->team->getTeamKey();
    }
    public function setTeamKey($key) { return; }

    protected $teamReport = null;
    
    public function getReport()
    {
        if ($this->teamReport) return $this->teamReport;
        
        $data = $this->get('report');
        if (!is_array($data)) $data = array();
        
        $this->teamReport = new TeamReport();
        
        $this->teamReport->setData($data);
        
        return $this->teamReport;
    }
    public function saveReport($teamReport = null)
    {
        if (!$teamReport) $teamReport = $this->teamReport;
    
        if (!$teamReport) return;
        
        $data = $teamReport->getData();
        
        $this->set('report',$data);
    }
}
?>
