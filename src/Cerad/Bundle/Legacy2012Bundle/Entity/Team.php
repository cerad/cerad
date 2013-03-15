<?php
namespace Zayso\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Team extends BaseEntity
{
    const TypeSchedule = 'Schedule'; // Upper or lower case???
    const TypePhysical = 'Physical';
    const TypeEvent    = 'Event';
    const TypePool     = 'Pool';
    const TypePlayoff  = 'Playoff';
    
    const SourceEayso  = 'Eayso';
    const SourceImport = 'Import';
    const SourceAuto   = 'Auto';   // Automatically created by zayso
    const SourceManual = 'Manual'; // Maually created within zayso
    
    const LevelRegular  = 'Regular';
    const LevelSelect   = 'Import';
    const LevelExtra    = 'Extra';
    
    protected $id;
    
    protected $project;

    protected $parent = null;
    
    protected $org = null;
    
    protected $type = null;
    
    protected $source = null;
    protected $level  = self::LevelRegular;
    
    protected $key1 = null;
    protected $key2 = null;
    protected $key3 = null;
    protected $key4 = null;
    
    protected $desc1 = null;
    protected $desc2 = null;
    
    protected $age    = null;
    protected $gender = null;

    
    /**
     *   ORM\OneToMany(targetEntity="PersonTeamRel", mappedBy="team")
     */
    protected $personRels;
    
    protected $status = 'Active';
    
    protected $datax = null;
    
    public function __construct()
    {
        $this->personRels  = new ArrayCollection();
    }
    
    public function getId     () { return $this->id;      }
    public function getAge    () { return $this->age;     }
    public function getOrg    () { return $this->org;     }
    public function getType   () { return $this->type;    }
    public function getDesc1  () { return $this->desc1;   }
    public function getDesc2  () { return $this->desc2;   }
    public function getLevel  () { return $this->level;   }
    public function getGender () { return $this->gender;  }
    public function getSource () { return $this->source;  }
    public function getStatus () { return $this->status;  }
    public function getParent () { return $this->parent;  }
    public function getProject() { return $this->project; }
    
    public function setAge    ($value) { $this->onScalerPropertySet('age',    $value); }
    public function setOrg    ($value) { $this->onObjectPropertySet('org',    $value); }
    public function setType   ($value) { $this->onScalerPropertySet('type',   $value); }
    public function setDesc   ($value) { $this->onScalerPropertySet('desc1',  $value); }
    public function setDesc1  ($value) { $this->onScalerPropertySet('desc1',  $value); }
    public function setDesc2  ($value) { $this->onScalerPropertySet('desc2',  $value); }
    public function setLevel  ($value) { $this->onScalerPropertySet('level',  $value); }
    public function setGender ($value) { $this->onScalerPropertySet('gender', $value); }
    public function setSource ($value) { $this->onScalerPropertySet('source', $value); }
    public function setStatus ($value) { $this->onScalerPropertySet('status', $value); }
    public function setParent ($value) { $this->onObjectPropertySet('parent', $value); }
    public function setProject($value) { $this->onObjectPropertySet('project',$value); }
    
    // Custom getter/setters
    public function setKey($key) { $this->onScalerPropertySet('key1',$key); }
    public function getKey()     { return $this->key1; }
    
    public function getKeyx()
    {
        if ($this->key1)   return $this->key1;
        if ($this->parent) return $this->parent->getKeyx();
        return null;
    }
    public function setKeyx($value) {}
    
    public function setKey1($key) { $this->onScalerPropertySet('key1',$key); }
    public function getKey1()     { return $this->key1; }
    
    public function setKey2($key) { $this->onScalerPropertySet('key2',$key); }
    public function getKey2()     { return $this->key2; }
    
    public function setKey3($key) { $this->onScalerPropertySet('key3',$key); }
    public function getKey3()     { return $this->key3; }
    
    public function setKey4($key) { $this->onScalerPropertySet('key4',$key); }
    public function getKey4()     { return $this->key4; }
    
    public function setTeamKey($key) { $this->onScalerPropertySet('key1',$key); }
    public function getTeamKey()     { return $this->key1; }
     
    public function setTeamKeyExpanded($key) { $this->onScalerPropertySet('key2',$key); }
    public function getTeamKeyExpanded()     { return $this->key2; }
    
    public function setEaysoTeamId($key) { $this->onScalerPropertySet('key3',$key); }
    public function getEaysoTeamId()     { return $this->key3; }
    
    public function setEaysoTeamDesig($key) { $this->onScalerPropertySet('key4',$key); }
    public function getEaysoTeamDesig()     { return $this->key4; }
    
    public function getDesc() 
    { 
        if ($this->desc1) return $this->desc1;   
        return $this->key1;
    }
    
    public function setTeamName($name) { $this->set('teamName',$name); }
    public function getTeamName()      { return $this->get('teamName'); }
    
    public function setTeamColors($colors) { $this->set('teamColors',$colors); }
    public function getTeamColors()        { return $this->get('teamColors'); }
    
    public function setSfSP($score) { $this->set('sfSP',$score); }
    public function getSfSP()        { return $this->get('sfSP'); }
    
    public function setOrgDesc($desc) {}
    public function getOrgDesc() 
    {
        if (!$this->org) return null;
        return $this->org->getDesc2();
    }
    public function getParentTeamKey()
    {
        if (!$this->parent) return null;
        return $this->parent->getTeamKey();
    }
    public function getParentForType($type)
    {
        if ($this->type == $type) return $this;
        if ($this->parent) return $this->parent->getParentForType($type);
        return null;
    }
    public function setTypePhysical() { return $this->setType(self::TypePhysical); }
    public function setTypePool()     { return $this->setType(self::TypePool); }
    public function setTypePlayoff()  { return $this->setType(self::TypePlayoff); }
    
    public function setSourceImport() { return $this->setSource(self::SourceImport); }
    
    public function setLevelRegular() { return $this->setLevel(self::LevelRegular); }
    public function setLevelSelect () { return $this->setLevel(self::LevelSelect);  }
    public function setLevelExtra  () { return $this->setLevel(self::LevelExtra);   }
    
    /* ================================================================================
     * Used to consolidate team standings
     */
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
    public function clearReportInfo()
    {
        $this->set('report',null);
        $this->teamReport = null;
    }
 }
?>
