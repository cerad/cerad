<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Event extends BaseEntity
{
    protected $id;
    
    protected $project;
    
    protected $num = 0;
    
    protected $type = 'Game';
    
    protected $field = null;

    protected $org = null;
    
    protected $date = null;

    protected $time = null;
    
    protected $pool = null;
    
    protected $status = 'Active';
    
    protected $datax = null;
    
    protected $teams;

    protected $persons;
    
    public function __construct()
    {
        $this->teams  = new ArrayCollection();
        $this->person = new ArrayCollection();
    }
    // ================================
    // Team Stuff
    public function addTeam($team)
    {
        if (!$team) return;
        $this->teams[$team->getType()] = $team; // Not going to call change here, not a property?
    }
    public function getTeams() 
    { 
        $teams = $this->teams->toArray();
        
        usort($teams,array($this,'compareEventTeams'));
        
        return $teams; 
    }
    public function getEventTeams() { return $this->teams; }
    
    public function compareEventTeams($team1,$team2)
    {
        if ($team1->getType() == 'Home') return -1;
        if ($team2->getType() == 'Home') return  1;
        return strcmp($team1->getType(),$team2->getType());
    }
    public function getTeamForType($type)
    {
        if (isset($this->teams[$type])) return $this->teams[$type];
        return null;
    }
    public function getHomeTeam() { return $this->getTeamForType(EventTeam::TypeHome); }
    public function getAwayTeam() { return $this->getTeamForType(EventTeam::TypeAway); }

    // ================================
    // Person Stuff
    public function addPerson($person)
    {
        if (!$person) return;
        $this->persons[$person->getType()] = $person; // Not going to call change here, not a property?
    }
    // Might want to add some sort of sorting here?
    public function getPersons()      { return $this->getEventPersonsSorted(); }
    public function getEventPersons() { return $this->persons; }
    public function getPersonForType($type)
    {
        if (isset($this->persons[$type])) return $this->persons[$type];
        return null;
    }
    public function getEventPersonsSorted()
    {
      //return $this->persons;
        
        $types = array_keys(EventPerson::$typeDescs);
        $eventPersons = array();
        foreach($types as $type)
        {
            if (isset($this->persons[$type])) $eventPersons[$type] = $this->persons[$type];
          //if (isset($this->persons[$type])) $eventPersons[]      = $this->persons[$type];
        }
        return $eventPersons;
    }
    // ================================
    // Field stuff
    public function getFieldDesc()
    {
        if (!$this->field) return null;
        return $this->field->getKey();
    }
    public function setFieldDesc($desc) { return; }
    
    // ====================================================
    // getters/setters
    public function getId     () { return $this->id;      }
    public function getNum    () { return $this->num;     }
    public function getOrg    () { return $this->org;     }
    public function getType   () { return $this->type;    }
    public function getDate   () { return $this->date;    }
    public function getTime   () { return $this->time;    }
    public function getPool   () { return $this->pool;    }
    public function getField  () { return $this->field;   }
    public function getStatus () { return $this->status;  }
    public function getProject() { return $this->project; }
    
    public function setNum    ($value) { $this->onScalerPropertySet('num',    $value); }
    public function setOrg    ($value) { $this->onObjectPropertySet('org',    $value); }
    public function setType   ($value) { $this->onScalerPropertySet('type',   $value); }
    public function setDate   ($value) { $this->onScalerPropertySet('date',   $value); }
    public function setTime   ($value) { $this->onScalerPropertySet('time',   $value); }
    public function setPool   ($value) { $this->onScalerPropertySet('pool',   $value); }
    public function setField  ($value) { $this->onObjectPropertySet('field',  $value); }
    public function setStatus ($value) { $this->onScalerPropertySet('status', $value); }
    public function setProject($value) { $this->onObjectPropertySet('project',$value); }

    // The report comments
    public function setReport($value) { $this->set('report',$value); }
    public function getReport()       
    { 
        $report = $this->get('report');
        if ($report) return $report;
        
        $report = <<< EOT
Field Conditions: Okay.
    
Serious Injuries: None.

Misconduct: None.

EOT;
        return $report; 
        
    }
    
    // Report Status
    public function setReportStatus($value) { $this->set('reportStatus',$value); }
    
    /* =======================================================
     * Blank - Game has not yet been played
     * Future - Game not yet played
     * 
     * Submitted - Referee has submitted a report
     * Approved - Administrator has reviewed it
     * Not Required - For canceled games, scrimmages etc
     * 
     * Pending - Game has been played, no report submitted yet
     * Over Due - Game was played, should have been submitted
     */
    public function getReportStatus()
    {
        // See if already set
        $reportStatus = $this->get('reportStatus');// die('Report Status: ' . $reportStatus);
        if ($reportStatus) return $reportStatus;
        
        // Deal with cancelled or processed games
        // This got confusing so for now 
        $status = $this->getStatus();
        if ($status == 'Cancelled') return 'NotRequired';
        
        // See if game has been played, could use time as well
        $date = $this->getDate();
        $today = date('Ymd');
        if ($today >= $date) return 'Pending';
        
        return 'Future';
    }
    public function getPointsApplied()       { return $this->get('pointsApplied'); }
    public function setPointsApplied($value) { return $this->set('pointsApplied',$value); }
    public function isPointsApplied()
    {
        if ($this->get('pointsApplied') == 'Yes') return true;
        return false;
    }
    public function isPoolPlay()
    {
        if (strpos($this->pool,'PP') === false) return false;
        return true;
    }
    public function isSoccerfest()
    {
        if (strpos($this->pool,'Soccerfest') === false) return false;
        return true;
    }
}
?>
