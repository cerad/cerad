<?php

namespace Cerad\Bundle\GameV2Bundle\Entity;

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
    
    protected $season;
    protected $sport;
    protected $domain;    // Primary Domain, a project can still be associated with other domains
    protected $domainSub; // Same for sub domains
    
    protected $desc;      // Maybe title etc or just a general info block
    protected $title;
    protected $status = 'Active';
   
    protected $data;      // Additional attributes from a yml file
    
    /* ===========================================================
     * Getters/Setters
     */
    public function getId()        { return $this->id;     }
    public function getData()      { return $this->data;   }
    public function getDesc()      { return $this->desc;   }
    public function getTitle()     { return $this->title;  }
    public function getStatus()    { return $this->status; }
    
    public function getSeason()    { return $this->season; }
    public function getSport ()    { return $this->sport;  }
    public function getDomain()    { return $this->domain; }
    public function getDomainSub() { return $this->domainSub; }
    
    public function setId       ($value) { $this->onPropertySet('id',       $value); }
    public function setData     ($value) { $this->onPropertySet('data',     $value); }
    public function setDesc     ($value) { $this->onPropertySet('desc',     $value); }
    public function setTitle    ($value) { $this->onPropertySet('title',    $value); }
    public function setStatus   ($value) { $this->onPropertySet('status',   $value); }
    
    public function setSeason   ($value) { $this->onPropertySet('season',   $value); }
    public function setSport    ($value) { $this->onPropertySet('sport',    $value); }
    public function setDomain   ($value) { $this->onPropertySet('domain',   $value); }
    public function setDomainSub($value) { $this->onPropertySet('domainSub',$value); }
    
    /* =========================================
     * Allow sending in an array of data
     */
    public function __construct($data = null)
    {
        $this->data = $data;
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
