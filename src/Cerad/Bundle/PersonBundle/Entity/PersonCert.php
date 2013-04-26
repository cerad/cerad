<?php
namespace Cerad\Bundle\PersonBundle\Entity;

class PersonCert extends BaseEntity
{
    const FedAYSO = 'AYSO';
    const FedUSSF = 'USSF';
    const FedNFHS = 'NFHS';
       
    const RoleReferee    = 'Referee';
    const RoleSafeHaven  = 'Safe_Haven';
    
    const RoleAssignor   = 'Assignor';
    const RoleAssessor   = 'Assessor';
    const RoleInstructor = 'Instructor';
    
    const BadgeGrade8 = 'Grade 8';
    
    protected $id;
    
    // Combo of FED ROLE IDX will be unique
    // AYSO Referee 12341234
    
    protected $fed;        // federation AYSO USSF NFHS
    protected $role;       // Referee, Assessor etc
    protected $identifier; // ayso id, ussf etc, not sure if it should be unique or not
    
    protected $sort;   // Maybe
   
    protected $person;
    
    protected $badgex;  // As set by user
    protected $badge;   // As set by administrator
    
    protected $dateFirstCertified;
    protected $dateLastUpgraded;
    protected $upgrading;
 
    protected $status   = 'Checking';
    protected $verified = 'No';
    
    /* =================================================================
     * Accessors
     */
    public function getId     () { return $this->id;      }
    public function getFed    () { return $this->fed;     }
    public function getRole   () { return $this->role;    }
    public function getPerson () { return $this->person;  }
    
    public function getBadgex () { return $this->badgex;  }
    public function getBadge  () { return $this->badge;   }

    public function getStatus  () { return $this->status;  }
    public function getVerified() { return $this->verified;}
    
    public function getDateFirstCertified () { return $this->dateFirstCertified;  }
    public function getDateLastUpgraded()    { return $this->dateLastUpgraded;    }
    public function getUpgrading()           { return $this->upgrading;           }
    
    public function getIdentifier() { return $this->identifier; }
    
    public function setFed    ($value) { $this->onPropertySet('fed',     $value); }
    public function setRole   ($value) { $this->onPropertySet('role',    $value); }
    public function setBadge  ($value) { $this->onPropertySet('badge',   $value); }
    public function setStatus ($value) { $this->onPropertySet('status',  $value); }
    public function setVerified($value){ $this->onPropertySet('verified',$value); }
    
    public function setDateFirstCertified($value) { $this->onPropertySet('dateFirstCertified',$value); }
    public function setDateLastUpgraded  ($value) { $this->onPropertySet('dateLastUpgraded',  $value); }
    public function setUpgrading         ($value) { $this->onPropertySet('upgrading',         $value); }
    
    public function setBadgex($badge) 
    { 
        $this->onPropertySet('badgex',$badge); 
    
        if (!$this->badge) $this->onPropertySet('badge',$badge); 
    }
    public function setPerson($person) 
    { 
        $this->onPropertySet('person',$person);
    }
    
    static public function createRefereeUSSF()
    {
        $cert = new self();
        $cert->setFed (self::FedUSSF);
        $cert->setRole(self::RoleReferee);
        
        return $cert;
    }
    static public function createRefereeAYSO()
    {
        $cert = new self();
        $cert->setFed (self::FedAYSO);
        $cert->setRole(self::RoleReferee);
        
        return $cert;
    }
    // Calc based on cert date
    public function getExperience()          
    {
        return $this->experience;
    }

    /* ====================================================================
     * Keep going back and forth on identifier
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
     * Before a person can be certified they must belong to a league
     * The identifier will come from the league so no extra processing here
     */
    public function setIdentifier($value)
    {
        // c for certified
        $this->onPropertySet('identifier',$value);
        
      //$this->onPropertySet('identifier',$this->fed . 'c' . $value);
    }
}
?>
