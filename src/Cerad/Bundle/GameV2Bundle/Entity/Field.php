<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Cerad\Bundle\CommonBundle\Functions\IfIsSet;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityPrimary as CommonBaseEntityPrimary;

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
class Field extends CommonBaseEntityPrimary
{    
    protected $url;
    protected $venue;      // John Hunt
    protected $latitude;
    protected $longitude;
    
    protected $checkConflicts = true;
    
    protected $fieldFields1;
    protected $fieldFields2;
    protected $fieldProjects;
    
    public function getUrl      ()   { return $this->url;  }
    public function getVenue    ()   { return $this->venue;     }
    public function getLatitude ()   { return $this->latitude; }
    public function getLongitude()   { return $this->longitude; }
        
    public function getCheckConflicts() { return $this->checkConflicts; }
    
    public function getFieldFields1 () { return $this->fieldFields1;   }
    public function getFieldFields2 () { return $this->fieldFields2;   }
    public function getFieldProjects() { return $this->fieldProjects; }
       
    public function setUrl      ($value) { $this->onPropertySet('url',      $value); }
    public function setVenue    ($value) { $this->onPropertySet('venue',    $value); }
    public function setLatitude ($value) { $this->onPropertySet('latitude', $value); }
    public function setLongitude($value) { $this->onPropertySet('longitude',$value); }
        
    public function setCheckConflicts($value) { $this->onPropertySet('checkConflicts',$value); }
       
    /* =========================================================
     * 
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->fieldFields1  = new ArrayCollection();
        $this->fieldFields2  = new ArrayCollection();
        $this->fieldProjects = new ArrayCollection();
    }
    public function newIdentifier() { return new FieldIdentifier(); }
    
    public function addProject(Project $item,$role = null)
    {
        // Works
        return $this->addRelItem('fieldProjects',__NAMESPACE__.'\\ProjectField',$item,$role);
    }
    public function addField1(Field $item,$role = null)
    {
        // Works
        return $this->addRelItem('fieldFields1',__NAMESPACE__.'\\FieldField',$item,$role);
    }
    public function addField2(Field $item,$role = null)
    {
        // Works
        return $this->addRelItem('fieldFields2',__NAMESPACE__.'\\FieldField',$item,$role);
    }
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Field %s %s %s\n",
            $this->status,
            $this->name,
            $this->id
        );
    }
    /* ===================================
     * Does it make sense to be able to load up an instance here?
     * Project is used to help generate an identifier is necessary
     * And could be used to attache the entity to the project?
     */
    public function loadFromArray($entityFixture,$project = null,$connectToProject = true)
    {
        // Entity
        $id     = IfIsSet::exe($entityFixture,'id');
        $name   = IfIsSet::exe($entityFixture,'name');
        $desc   = IfIsSet::exe($entityFixture,'desc');
        $status = IfIsSet::exe($entityFixture,'status');
        
        $url       = IfIsSet::exe($entityFixture,'url');
        $latitude  = IfIsSet::exe($entityFixture,'latitude');
        $longitude = IfIsSet::exe($entityFixture,'longitude');
        
        if ($id)     $this->setId    ($id);
        if ($status) $this->setStatus($status);

        $this->setName($name);
        $this->setDesc($desc);
        
        $this->setUrl      ($url);
        $this->setLatitude ($latitude);
        $this->setLongitude($longitude);
         
        // Idnetifiers
        $identifiersFixture = IfIsSet::exe($entityFixture,'identifiers',array());
        foreach($identifiersFixture as $identifierFixture)
        {
            $entity = $this;
            
                $value  = IfIsSet::exe($identifierFixture,'value' );
                $source = IfIsSet::exe($identifierFixture,'source');
                $status = IfIsSet::exe($identifierFixture,'status');
                
                if (!$value)
                {
                    // More or less arbiter style, name avaiable there
                    // $value = $manager->hash(array($source,$sport,$season,$domain,$domainSub));
                    $values = array();
                    $values[] = $source;
 
                    if (!$project) $values[] = 'Fake';
                    else
                    {
                        $values[] = $project->getSeason();
                    }
                    $values[] = $entity->getName();
                    
                    $value = $this->hash($values);
                }
                $identifier = $this->newIdentifier();
                
                $identifier->setValue ($value);
                $identifier->setSource($source);
                
                if ($status) $identifier->setStatus($status);
                
                $this->addIdentifier($identifier);                        
        }
        // Probably going to far
        if ($project && $connectToProject)
        {
            $project->addField($this);
        }
        return $this;
    }
}
?>
