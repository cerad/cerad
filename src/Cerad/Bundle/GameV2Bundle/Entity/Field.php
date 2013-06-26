<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

/* =======================================================
 * In most cases, fields don't change much from year to year
 * 
 * NatGames ia an extreme case of them changing since each tournament is in a different part of the country
 * S5Games not quite as extreme since venues typically change every two years
 * Area5C tournaments tend to flip between two venues
 * 
 * ProjectField may or may not be neeeded
 * 
 * Tieing fields to a season allows defining fields on the fly
 * 
 * The link parameter allows tieing fields together
 * 
 * Be a little careful on venue, two domains could have venues with same name
 * 
 * Having a sport does not make much sense?
 * 
 * SubDoman is questionalble, arbiter reqquires unique names withing a domain.
 * AHSAA has very little overlap with club teams.
 * 
 * Venue needs work
 * Arbiter has the (largly unused) capability of defining a venue and then fields within that venue.
 * Handy to have for sport complexes.
 */
class Field extends BaseEntity
{
    protected $id;
    protected $projectFields;
    protected $identifiers;
    
    protected $name;       // John Hunt 1
    protected $desc;
    protected $venue;      // John Hunt
    
    protected $url;
    protected $latitude;
    protected $longitude;
    
    protected $checkConflicts = true;
    
    protected $linkField;   // Different name but same field
    protected $linkVenue;   // Different name but same venue
    protected $linkOverlap; // Allows grouping different fields that physically overlap each other
    
    protected $season;
    protected $domain;
    protected $domainSub;
    
    protected $status = 'Active';
    
    public function getId  ()      { return $this->id;   }
    public function getUrl ()      { return $this->url;  }
    public function getName()      { return $this->name; }
    public function getDesc()      { return $this->desc; }
    
    public function getLatitude ()   { return $this->latitude; }
    public function getLongitude()   { return $this->longitude; }
    
    public function getLinkField()   { return $this->linkField; }
    public function getLinkVenue()   { return $this->linkVenue; }
    public function getLinkOverlap() { return $this->linkOverlap; }
    
    public function getCheckConflicts() { return $this->checkConflicts; }
    
    public function getVenue()     { return $this->venue;     }
    public function getStatus()    { return $this->status;    }
    
    public function getSeason()    { return $this->season;    }
    public function getDomain()    { return $this->domain;    }
    public function getDomainSub() { return $this->domainSub; }
    
    public function getProjectFields() { return $this->projectFields; }
    public function getIdentifiers  () { return $this->identifiers;   }
    
    public function setId       ($value) { $this->onPropertySet('id',       $value); }
    public function setName     ($value) { $this->onPropertySet('name',     $value); }
    public function setDesc     ($value) { $this->onPropertySet('desc',     $value); }
    
    public function setUrl      ($value) { $this->onPropertySet('url',      $value); }
    public function setLatitude ($value) { $this->onPropertySet('latitude', $value); }
    public function setLongitude($value) { $this->onPropertySet('longitude',$value); }
    
    public function setLinkField  ($value) { $this->onPropertySet('linkField',  $value); }
    public function setLinkVenue  ($value) { $this->onPropertySet('linkVenue',  $value); }
    public function setLinkOverlap($value) { $this->onPropertySet('linkOverlap',$value); }
    
    public function setCheckConflicts($value) { $this->onPropertySet('checkConflicts',$value); }
    
    public function setVenue    ($value) { $this->onPropertySet('venue',    $value); }
    public function setStatus   ($value) { $this->onPropertySet('status',   $value); }
    
    public function setSeason   ($value) { $this->onPropertySet('season',   $value); }
    public function setDomain   ($value) { $this->onPropertySet('domain',   $value); }
    public function setDomainSub($value) { $this->onPropertySet('domainSub',$value); }
    
    /* =========================================================
     * 
     */
    public function __construct()
    {
        $this->id          = $this->genGUID();
        $this->projectFields = new ArrayCollection();
        $this->identifiers   = new ArrayCollection();
    }
    public function addIdentifier(FieldIdentifier $identifier)
    {
        $this->identifiers[] = $identifier;
        $identifier->setField($this);
    }
    public function addProjectField(ProjectField $projectField)
    {
        // Protect against dups
        if ($this->hasProject($projectField->getProject)) return $this;
        
        $this->projectFields[] = $projectField;
        $projectField->setField($this);
        return $this;
    }
    public function hasProject(Project $project)
    {
        foreach($this->projectFields as $projectField)
        {
            if ($projectField->getProject->getId() == $project->getId()) return true;
        }
        return false;
    }
   /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Field   %-8s %-8s %-10s %s %s %s\n",
            $this->status,
            $this->domain,
            $this->domainSub,
            $this->season,
            $this->name,
            $this->id
        );
    }
}
?>
