<?php
namespace Cerad\Bundle\PersonBundle\Entity;

/* ===============================================================
 * Still look for a better word than League aka AYSO region, USSF state etc.
 * Used to call it organization 
 * But league has sort of sports ring to it
 * 
 * A person can be registered in one of more leagues either as a player or as
 * some sort of volunteer/official/coach/administrator etc
 * 
 * Leagues use globally unique identifiers
 * 
 * Want to break up security (background checks) and registration?
 */
class PersonLeague extends BaseEntity
{
    const FedAYSO = 'AYSO';
    const FedUSSF = 'USSF';
    const FedNFHS = 'NFHS';
       
    const RolePlayer     = 'Player';
    const RoleVolunteer  = 'Volunteer';  // Works for AYSO
    const RoleContractor = 'Contractor'; // Works for USSF
    
    const RoleSecurity   = 'Security';  // Background check?
    
    protected $id;
    
    // Combo of FED ROLE IDX will be unique
    // AYSO Referee 12341234
    
    protected $fed;        // federation AYSO USSF NFHS
    protected $role;       // Referee, Assessor etc
    protected $identifier; // ayso id, ussf etc, not sure if it should be unique or not
    
    protected $league;
    protected $person;

    protected $cvpa;       // Child training
    
    protected $memId;      // Just the aysoid or ussfid.  No prefix
    protected $memYear;    // FS2012 etc
    
    protected $memFirstRegistered; // Registered or whatever
    protected $memLastRegistered;
    protected $memExpires;
    
    // Probably want two objects
    protected $backgroundCheckFirst;
    protected $backgroundCheckLast;
    protected $backgroundCheckExpires;
    
    protected $status   = 'Active'; // Active means all is well, Checking for needs to be checked
    protected $verified = 'No';     // Active means all is well, Checking for needs to be checked
    
    /* =================================================================
     * Accessors
     */
    public function getId     () { return $this->id;      }
    public function getFed    () { return $this->fed;     }
    public function getRole   () { return $this->role;    }
    public function getCvpa   () { return $this->cvpa;    }
    public function getPerson () { return $this->person;  }
    public function getLeague () { return $this->league;  }
    
    public function getMemId     () { return $this->memId;  }
    public function getMemYear   () { return $this->memYear;  }
    public function getIdentifier() { return $this->identifier; }
    
    public function getStatus  () { return $this->status;   }
    public function getVerified() { return $this->verified; }
    
    public function getDateFirst()   { return $this->dateFirst;   }
    public function getDateLast()    { return $this->dateLast;    }
    public function getDateExpires() { return $this->dateExpires; }
    
    public function setFed    ($value) { $this->onPropertySet('fed',     $value); }
    public function setRole   ($value) { $this->onPropertySet('role',    $value); }
    public function setCvpa   ($value) { $this->onPropertySet('cvpa',    $value); }
    public function setBadge  ($value) { $this->onPropertySet('badge',   $value); }
    public function setStatus ($value) { $this->onPropertySet('status',  $value); }
    public function setVerified($value){ $this->onPropertySet('verified',$value); }
    
    public function setMemYear($value) { $this->onPropertySet('memYear', $value); }
    
    public function setDateFirst  ($value) { $this->onPropertySet('dateFirst',  $value); }
    public function setDateLast   ($value) { $this->onPropertySet('dateLast',   $value); }
    public function setDateExpores($value) { $this->onPropertySet('dateexpires',$value); }
    
    public function setPerson($value) { $this->onPropertySet('person',$value); }
    public function setLeague($value) { $this->onPropertySet('league',$value); }
    
    /* =======================================================
     * Think I need a factory or maybe move this to the repository?
     */
    static public function createContractorUSSF()
    {
        $item = new self();
        $item->setFed (self::FedUSSF);
        $item->setRole(self::RoleContractor);
        
        return $item;
    }
    static public function createVolunteerAYSO()
    {
        $item = new self();
        $item->setFed (self::FedAYSO);
        $item->setRole(self::RoleVolunteer);
        
        return $item;
    }
    static public function createPlayerAYSO()
    {
        $item = new self();
        $item->setFed (self::FedAYSO);
        $item->setRole(self::RolePlayer);
        
        return $item;
    }
    /* ====================================================================
     * Keep going bavk and forth on identifier
     * It really shoud be globally unique for a person
     * However it does cause pain when dealing with the prefix
     * 
     * And for AYSO we also have volunter vs player
     * Technically should use AYSOV especially since the registration object can have both
     * On the other hand, don't really have national ussf players
     * And having AYSO is nice
     * 
     * If ayso player and volunteer numbers don't overlap then okay
     * Maybe a fedx? AYSOV
     * 
     * Now storing the member id as well as identifier
     */
    public function genIdentifier()
    {
        return $this->fed . $this->role[0] . $this->memId;
    }
    public function setIdentifier($value)
    {
        return $this->onPropertySet('identifier',$value);
    }
    public function setMemId($value) 
    { 
        $this->onPropertySet('memId', $value); 
        return $this->setIdentifier($this->genIdentifier());
    }
}
?>
