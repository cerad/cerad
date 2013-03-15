<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

class PersonReg extends BaseEntity
{
    const TypeAYSOV   = 'AYSOV'; // Volunteer
    const TypeAYSOP   = 'AYSOP'; // Player
    const TypeUSSF    = 'USSF';  // USSF
    const TypeNFHS    = 'NFHS';  // High School
    const TypeNISOA   = 'NISOA'; // College
    const TypeARBITER = 'ARBITER'; // Arbiter email
   
    protected $id;

    protected $person;

    protected $regType = 'BASE';

    protected $regKey = null;
  
    protected $org = null;

    protected $verified = 'No';

    // Experiment for single table inheritance
    protected $discr;
    
    public function setPerson($person) { $this->onObjectPropertySet('person',$person); }
    public function getPerson()        { return $this->person; }
    
    public function setRefBadge($refBadge) { return $this->set('ref_badge',$refBadge); }
    public function getRefBadge()          { return $this->get('ref_badge'); }

    public function setRefDate($refDate)  { return $this->set('ref_date',$refDate); }
    public function getRefDate()          { return $this->get('ref_date'); }
    
    public function setMemYear($memYear)  { return $this->set('mem_year',$memYear); }
    public function getMemYear()          { return $this->get('mem_year'); }
    
    /* ==============================================================
     * Org might be optional for some types of certs
     */
    public function setOrg($org) { $this->onObjectPropertySet('org',$org); }
    
    protected $orgTemp = null;
    
    public function getOrg() 
    { 
        if ($this->org)     return $this->org; 
        if ($this->orgTemp) return $this->orgTemp; 
        
        $this->orgTemp = new Org();
        return $this->orgTemp; 
   }
   // Usefull because the org is (AYSOR0894) is a generated value
    public function getOrgKey()
    {
        if ($this->org) return $this->org->getId();
        return null;
    }
    // Fake to keep forms happy (for now)
    public function setOrgKey($key) { }
    
    public function getId() { return $this->id; }

    // regType is basically a constant
    //public function setRegType($regType) { $this->onScalerPropertySet('regType',$regType); }
    
    public function getRegType()         { return $this->regType; }
    
    public function setRegKey($regKey) { $this->onScalerPropertySet('regKey',$regKey); }
    public function getRegKey()        { return $this->regKey; }

    public function setVerified($verified) { $this->onScalerPropertySet('verified',$verified); }
    public function getVerified()          { return $this->verified; }
    
    public function isAYSOV() { return false; }
    public function isUSSF () { return false; }

}