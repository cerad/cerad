<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Collections\ArrayCollection;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityPrimary as CommonBaseEntityPrimary;

use Cerad\Bundle\CommonBundle\Functions\IfIsSet;

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
 */

class Project extends CommonBaseEntityPrimary
{   
    protected $sport;
    protected $source;   // Arbiter, Zayso etc
    protected $season;
    protected $domain;    // Primary Domain, a project can still be associated with other domains
    protected $domainSub; // Same for sub domains
    
    protected $desc;
    protected $status = 'Active';
   
    protected $projectTeams;
    protected $projectLevels;
    protected $projectFields;
    
    protected $data;      // Additional attributes from a yml file
    
    /* ===========================================================
     * Getters/Setters
     */
    public function getDesc()      { return $this->desc;   }
    public function getData()      { return $this->data;   }
    public function getStatus()    { return $this->status; }
    
    public function getSport ()    { return $this->sport;  }
    public function getSource()    { return $this->source; }
    public function getSeason()    { return $this->season; }
    public function getDomain()    { return $this->domain; }
    public function getDomainSub() { return $this->domainSub; }
    
    public function getProjectTeams () { return $this->projectTeams;  }
    public function getProjectLevels() { return $this->projectLevels; }
    public function getProjectFields() { return $this->projectFields; }
    
    public function setId       ($value) { $this->onPropertySet('id',       $value); }
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
        parent::__construct();
        
        $this->data = $data;
        
        $this->projectTeams  = new ArrayCollection();
        $this->projectLevels = new ArrayCollection();
        $this->projectFields = new ArrayCollection();
    }
    public function newIdentifier() { return new ProjectIdentifier(); }

    public function addField(Field $item,$role = null)
    {
        return $this->addRelItem('projectFields',__NAMESPACE__.'\\ProjectField',$item,$role);
    }
    public function addTeam(Team $item,$role = null)
    {
        return $this->addRelItem('projectTeams',__NAMESPACE__.'\\ProjectTeam',$item,$role);
    }
    /* ==========================================================
     * ProjectLevel relation
     */
    public function addLevel(Level $item)
    {
        // Protect against dups
        if ($this->hasLevel($item)) return $this;
   
        // Make new entity
        $rel = new ProjectLevel();
        $rel->setProject($this);
        $rel->setLevel  ($item);

        $this->projectLevels[] = $rel;
        
        $this->onPropertyChanged('projectLevels');
     
        return $this;
    }
    public function hasLevel(Level $item)
    {
        foreach($this->projectLevels as $rel)
        {
            if ($rel->getLevel()->getId() == $item->getId()) return true;
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
    public function loadFromArray($myFixture,$project = null,$connectToProject = false)
    {
        // Non-standard properties
        $this->setData     (IfIsSet::exe($myFixture,'data'     ));
        $this->setSport    (IfIsSet::exe($myFixture,'sport'    ));
        $this->setSource   (IfIsSet::exe($myFixture,'source'   ));
        $this->setSeason   (IfIsSet::exe($myFixture,'season'   ));
        $this->setDomain   (IfIsSet::exe($myFixture,'domain'   ));
        $this->setDomainSub(IfIsSet::exe($myFixture,'domainSub'));
 
        // Maybe for cloning?
        if (!$project) $project = $this;
        
        return parent::loadFromArray($myFixture,$project);
    }
    public function genIdentifierValue($project)
    {
        // Maybe a clone?
        if (!$project) $project = $this;
        
        $values = array();
        $values[] = $project->getSource();
        if ($project->getName()) $values[] = $project->getName();
        else
        {
            $values[] = $project->getSport();
            $values[] = $project->getSeason();
            $values[] = $project->getDomain();
            $values[] = $project->getDomainSub();
        }
        return $this->hash($values);        
    }
}
?>
