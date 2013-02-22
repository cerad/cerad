<?php
namespace Cerad\Bundle\GameBundle\Entity;

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
    
    protected $hash;
    
    protected $name;       // John Hunt  1
    protected $venue;      // John Hunt
    protected $venueSub;   // 1
    
    protected $overlap;    // Allows grouping different fields that physically overlap each other
    protected $linkField;  // Different name but same field
    protected $linkVenue;  // Different name but same venue
    
    protected $season;
    protected $domain;
    protected $domainSub;
    
    protected $status;
    
    public function getId()        { return $this->id;   }
    public function getName()      { return $this->name; }
    
    public function getOverlap()   { return $this->overlap;   }
    public function getLinkField() { return $this->linkField; }
    public function getLinkVenue() { return $this->linkVenue; }
    
    public function getVenue()     { return $this->venue;     }
    public function getVenueSub()  { return $this->venueSub;  }
    public function getStatus()    { return $this->status;    }
    
    public function getDomain()    { return $this->domain; }
    public function getDomainSub() { return $this->domainSub; }
    public function getSeason()    { return $this->season;  }
    
    public function setName     ($value) { $this->onPropertySet('name',     $value); }
    public function setOverlap  ($value) { $this->onPropertySet('overlap',  $value); }
    public function setLinkField($value) { $this->onPropertySet('linkField',$value); }
    public function setLinkVenue($value) { $this->onPropertySet('linkVenue',$value); }
    public function setVenue    ($value) { $this->onPropertySet('venue',    $value); }
    public function setVenueSub ($value) { $this->onPropertySet('venueSub', $value); }
    public function setStatus   ($value) { $this->onPropertySet('status',   $value); }
    
    public function setDomain   ($value) { $this->onPropertySet('domain',   $value); }
    public function setDomainSub($value) { $this->onPropertySet('domainSub',$value); }
    public function setSeason   ($value) { $this->onPropertySet('season',   $value); }
    
    /* =======================================
     * Hashing stuff
     */
    public function getHash () { return $this->hash;  }
    
    public function setHash($value) { $this->onPropertySet('hash',$value); }
    
    static function genHash($params)
    {
        // Deal with venue vs names
        $params = self::processNameVenue($params);
        
        $paramsx = array($params['domain'],$params['domainSub'],$params['season'],$params['name']);
        
        return self::hash($paramsx);
    }
    /* =======================================
     * Create factory
     */
    static function create($params)
    {
        // Deal with venue vs names
        $params = self::processNameVenue($params);
        
         /* ===================================
         * Standard create
         */
        $item = new self();
        
        $item->setName     ($params['name']);     // John Hunt 1 Name is always the full unique name
        $item->setVenue    ($params['venue']);    // John Hunt
        $item->setVenueSub ($params['venueSub']); // 1
        
        $item->setSeason   ($params['season']);
        $item->setDomain   ($params['domain']);
        $item->setDomainSub($params['domainSub']);
        $item->setName     ($params['name']);
        
        if (isset($params['status'])) $item->setStatus($params['status']);
        else                          $item->setStatus('Active');
        
        $item->setHash(self::genHash($params));
        
        return $item;
    }
    /* =======================================
     * Utility to deal with name/venue/venueSub
     * Probably does not belong here
     */
    static function processNameVenue($params)
    {
        $paramsx = array();
        $paramsx['season']    = $params['season'];
        $paramsx['domain']    = $params['domain'];
        $paramsx['domainSub'] = $params['domainSub'];
        
        /* ==============================================
         * Allow either a straight name or a venue/venueSub
         */
        if (isset($params['name']))  $name = $params['name'];
        else                         $name = null;
        
        if (isset($params['venue'])) $venue = $params['venue'];
        else                         $venue = null;
        
        if (isset($params['venueSub'])) $venueSub = $params['venueSub'];
        else                            $venueSub = null;
        
        if (!$name)
        {
            if ($venueSub) $name = $venue . ', ' . $venueSub;  // Matches arbiter format
            else           $name = $venue;
        }
        // Generated return values
        $paramsx['name']     = $name;
        $paramsx['venue']    = $venue;
        $paramsx['venueSub'] = $venueSub;
        
        return $paramsx;
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
