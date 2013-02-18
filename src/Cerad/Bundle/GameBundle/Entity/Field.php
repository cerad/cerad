<?php
namespace Cerad\Bundle\GameBundle\Entity;

/* =======================================================
 * In most cases, fields don't change much from year to year
 * 
 * NatGames ia an extreme case of them changing
 * S5Games not quite as extreme
 * 
 * Tieing fields to a season allows defining fields on the fly
 * 
 * The link parameter allows tieing fields together
 * 
 * Be a little careful on venue, two domains could have venues with same name
 * 
 * Having a sport does not make much sense?
 * 
 * SubDoman is questionalble
 * AHSAA has very little overlap
 * 
 * Venue needs work
 */
class Field extends BaseEntity
{
    protected $id;
    
    protected $hash;
    
    protected $name;
    
    protected $overlap;    // Allows grouping different fields that physically overlap each other
    protected $linkField;  // Different name but same field
    protected $linkVenue;  // Different name but same venue
    
    protected $venue;
    
    protected $season;
    protected $domain;
    protected $domainSub;
    
    protected $status;
    
    public function getId()        { return $this->id;   }
    public function getName()      { return $this->name; }
    public function getOverlap()   { return $this->overlap;   }
    public function getLinkField() { return $this->linkField; }
    public function getLinkVenue() { return $this->linkVenue; }
    public function getVenue()     { return $this->name; }
    public function getStatus()    { return $this->status;  }
    
    public function getDomain()    { return $this->domain; }
    public function getDomainSub() { return $this->domainSub; }
    public function getSeason()    { return $this->season;  }
    
    public function setName     ($value) { $this->onPropertySet('name',     $value); }
    public function setOverlap  ($value) { $this->onPropertySet('overlap',  $value); }
    public function setLinkField($value) { $this->onPropertySet('linkField',$value); }
    public function setLinkVenue($value) { $this->onPropertySet('linkVenue',$value); }
    public function setVenue    ($value) { $this->onPropertySet('venue',    $value); }
    public function setStatus   ($value) { $this->onPropertySet('status',   $value); }
    
    public function setDomain   ($value) { $this->onPropertySet('domain',   $value); }
    public function setDomainSub($value) { $this->onPropertySet('domainSub',$value); }
    public function setSeason   ($value) { $this->onPropertySet('season',   $value); }
    
    /* =======================================
     * Hashing stuff
     */
    public function getHash () { return $this->hash;  }
    
    public function setHash($value) { $this->onPropertySet('hash',$value); }
    
    public function genHash()
    {
        // Basically a Project Field but without the sport
        return self::hash(array($this->season,$this->domain,$this->domainSub,$this->name));
    }
    /* =======================================
     * Create factory
     */
    static function create($season, $domain, $domainSub, $name, $status = 'Active')
    {
        $item = new self();
        
        $item->setName     ($name);
        $item->setDomain   ($domain);
        $item->setDomainSub($domainSub);
        $item->setSeason   ($season);
        $item->setStatus   ($status);
        
        $item->setHash($item->genHash());
        
        return $item;
    }
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Field   %-8s %-8s %-10s %s %s\n",
            $this->status,
          //$this->hashField,
            $this->domain,
            $this->domainSub,
            $this->season,
            $this->name
        );
    }
}
?>
