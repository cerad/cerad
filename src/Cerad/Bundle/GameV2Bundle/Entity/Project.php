<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/* ==============================================
 * A project hold specific season information
 * 
 * A project can be associated with multiple domains
 * 
 * Games within a project are administered by one sub domain
 * 
 * Not sure if want to have an actual link to an admin domain or not
 * 
 * Games will have both a project reference and a level reference
 * Think you can get the admin info from that
 * 
 * Older hash examples
 * 
+-----------------------------------------------+
| hash_project                                  |
+-----------------------------------------------+
| SP2013SOCCERALYSAUBURN                        |
| SP2013SOCCERALYSBIRMINGHAM                    |
| SP2013SOCCERALYSGADSDEN                       |
| SP2013SOCCERALYSHUNTSVILLE                    |
| SP2013SOCCERALYSLALIGABIRMINGHAMADULTAMATEURS |
| SP2013SOCCERALYSMOBILE                        |
| SP2013SOCCERALYSMONTGOMERY                    |
| SP2013SOCCERALYSSRAGAMES                      |
| SP2013SOCCERALYSTUSCALOOSA                    |
| SP2013SOCCERNASOAAHSAA                        |
| SP2013SOCCERNASOAMSSL                         |
| SP2013SOCCERNASOAUSSFHASL                     |
| SP2013SOCCERNASOAUSSFHASLWOMEN'S              |
+-----------------------------------------------+
 */
class Project extends BaseEntity
{
    protected $id;       // GUID - Either user readable or hash
    protected $identifiers;
    
    protected $sport;
    protected $source;   // Arbiter, Zayso etc
    protected $season;
    protected $domain;    // Primary Domain, a project can still be associated with other domains
    protected $domainSub; // Same for sub domains
    
    protected $name;
    protected $desc;
    protected $status = 'Active';
   
    protected $projectTeams;
    protected $projectLevels;
    protected $projectFields;
    
    protected $data;      // Additional attributes from a yml file
    
    /* ===========================================================
     * Getters/Setters
     */
    public function getId()        { return $this->id;     }
    public function getName()      { return $this->name;   }
    public function getDesc()      { return $this->desc;   }
    public function getData()      { return $this->data;  }
    public function getStatus()    { return $this->status; }
    
    public function getSport ()    { return $this->sport;  }
    public function getSource()    { return $this->source; }
    public function getSeason()    { return $this->season; }
    public function getDomain()    { return $this->domain; }
    public function getDomainSub() { return $this->domainSub; }
    
    public function getProjectTeams () { return $this->projectTeams;  }
    public function getProjectLevels() { return $this->projectLevels; }
    public function getProjectFields() { return $this->projectFields; }
    public function getIdentifiers()   { return $this->identifiers;   }
    
    public function setId       ($value) { $this->onPropertySet('id',       $value); }
    public function setName     ($value) { $this->onPropertySet('name',     $value); }
    public function setDesc     ($value) { $this->onPropertySet('desc',     $value); }
    public function setData     ($value) { $this->onPropertySet('data',     $value); }
    public function setStatus   ($value) { $this->onPropertySet('status',   $value); }
    
    public function setSport    ($value) { $this->onPropertySet('sport',    $value); }
    public function setSource   ($value) { $this->onPropertySet('source',   $value); }
    public function setSeason   ($value) { $this->onPropertySet('season',   $value); }
    public function setDomain   ($value) { $this->onPropertySet('domain',   $value); }
    public function setDomainSub($value) { $this->onPropertySet('domainSub',$value); }
    
    /* =========================================
     * Allow sending in an array of data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
        
        $this->id = $this->genGUID();
        $this->identifiers = new ArrayCollection();
        
        $this->projectTeams  = new ArrayCollection();
        $this->projectLevels = new ArrayCollection();
        $this->projectFields = new ArrayCollection();
    }
    public function addIdentifier(ProjectIdentifier $identifier)
    {
        $this->identifiers[] = $identifier;
        $identifier->setProject($this);
    }
    /*
    public function addTeam(Team $entity)
    {
        $this->teams[] = $entity;
        $entity->setProject($this);
    }
    public function addLevel(Level $entity)
    {
        $this->levels[] = $entity;
        $entity->setProject($this);
    }*/
    /* =============================================
     * Add a field to the project
     * Creates the ProjectField if necessary
     * 
     * Does not update the fields list of projects
     * Probly need add project field
     */
    public function addField(Field $field)
    {
        $debug = false;
        if ($field->getName() == 'Forest Park')
        {
            echo sprintf("addField %s %s\n",$this->id,$field->getName());
            $debug = false;
        }
        // Protect against dups
        if ($this->hasField($field)) return $this;
        if ($debug) echo sprintf("### %s Not already in collection\n",$field->getName());
        
        // Make new entity
        $projectField = new ProjectField();
        $projectField->setProject($this);
        $projectField->setField  ($field);
        $projectField->setName   ($field->getName());
        
        $this->projectFields[] = $projectField;
        
        if ($debug) echo sprintf("### %s Was added\n",$field->getName());
        
        // This is very important, need to trigger when have changes
        $this->onPropertyChanged('projectFields',null,null);
        
        return $this;
    }
    public function hasField(Field $field)
    {
        foreach($this->projectFields as $projectField)
        {
            if ($projectField->getField()->getId() == $field->getId()) return true;
        }
        return false;
    }
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Project %-8s %-8s %-8s %-10s %s #%s\n",
            $this->status,
            $this->season,
            $this->sport,
            $this->domain,
            $this->domainSub,
            $this->id
        );
    }
}
?>