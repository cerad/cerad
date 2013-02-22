<?php

namespace Cerad\Bundle\GameBundle\Entity;

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
    protected $id;
    
    protected $season;
    protected $sport;
    protected $domain;    // Primary Domain, a project can still be associated with other domains
    protected $domainSub; // Same for sub domains
    
    protected $hash;
    
    protected $desc;      // Maybe title etc or just a general info block
    protected $status;
   
    /* ===========================================================
     * Getters/Setters
     */
    public function getId()        { return $this->id;     }
    public function getDesc()      { return $this->desc;   }
    public function getStatus()    { return $this->status; }
    public function getSeason()    { return $this->season; }
    public function getSport ()    { return $this->sport;  }
    public function getDomain()    { return $this->domain; }
    public function getDomainSub() { return $this->domainSub; }
    
    public function setDesc     ($value) { $this->onPropertySet('desc',     $value); }
    public function setStatus   ($value) { $this->onPropertySet('status',   $value); }
    public function setSeason   ($value) { $this->onPropertySet('season',   $value); }
    public function setSport    ($value) { $this->onPropertySet('sport',    $value); }
    public function setDomain   ($value) { $this->onPropertySet('domain',   $value); }
    public function setDomainSub($value) { $this->onPropertySet('domainSub',$value); }
    
    /* =======================================
     * Hashing stuff
     */
    public function getHash () { return $this->hash;  }
    
    public function setHash($value) { $this->onPropertySet('hash',$value); }
    
    static function genHash($params)
    {
        $paramsx = array($params['sport'],$params['domain'],$params['domainSub'],$params['season']);
        
        return self::hash($paramsx);
    }
    
    static function create($params)
    {
        $item = new self();
        
        $item->setSeason   ($params['season']);
        $item->setSport    ($params['sport']);
        $item->setDomain   ($params['domain']);
        $item->setDomainSub($params['domainSub']);
        
        if (isset($params['status'])) $item->setStatus($params['status']);
        else                          $item->setStatus('Active');
        
        $item->setHash(self::genHash($params));
        
        return $item;
    }
    /* =========================================
     * Debugging
     */
    public function __toString()
    {
        return sprintf("Project %-8s %-8s %-8s %-10s %s\n",
            $this->status,
          //$this->hash,
            $this->season,
            $this->sport,
            $this->domain,
            $this->domainSub
        );
    }
}
?>
