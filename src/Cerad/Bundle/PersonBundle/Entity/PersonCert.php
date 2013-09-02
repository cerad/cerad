<?php
namespace Cerad\Bundle\PersonBundle\Entity;

class PersonCert extends BaseEntity
{       
    const RoleReferee    = 'Referee';
    const RoleSafeHaven  = 'SafeHaven';
    
    const RoleAssignor   = 'Assignor';
    const RoleAssessor   = 'Assessor';
    
    const RoleCoachInstructor   = 'CoachInstructor';
    const RoleRefereeInstructor = 'RefereeInstructor';
    
    protected $id;
    protected $fed;     // PersonFed
    protected $role;    // Referee, Assessor etc
    
    protected $sort;    // Maybe
    
    protected $badge;   // As set by administrator
    protected $badgex;  // As set by user
    
    protected $dateFirstCertified;
    protected $dateLastUpgraded;
    protected $dateExpires;
    
    protected $upgrading;
 
    protected $status   = 'Active';
    protected $verified = 'No';
    
    /* =================================================================
     * Accessors
     */
    public function getId     () { return $this->id;      }
    public function getFed    () { return $this->fed;     }
    public function getRole   () { return $this->role;    }
    
    public function getBadgex () { return $this->badgex;  }
    public function getBadge  () { return $this->badge;   }

    public function getStatus  () { return $this->status;  }
    public function getVerified() { return $this->verified;}
    
    public function getDateFirstCertified () { return $this->dateFirstCertified;  }
    public function getDateLastUpgraded()    { return $this->dateLastUpgraded;    }
    public function getDateExpires()         { return $this->dateExpires;         }
    public function getUpgrading()           { return $this->upgrading;           }
    
    public function setFed     ($value) { $this->onPropertySet('fed',       $value); }
    public function setRole    ($value) { $this->onPropertySet('role',      $value); }
    public function setBadge   ($value) { $this->onPropertySet('badge',     $value); }
    public function setStatus  ($value) { $this->onPropertySet('status',    $value); }
    public function setVerified($value) { $this->onPropertySet('verified',  $value); }
    
    public function setDateFirstCertified($value) { $this->onPropertySet('dateFirstCertified',$value); }
    public function setDateLastUpgraded  ($value) { $this->onPropertySet('dateLastUpgraded',  $value); }
    public function setDateExpires       ($value) { $this->onPropertySet('dateExpires',       $value); }
    public function setUpgrading         ($value) { $this->onPropertySet('upgrading',         $value); }
    
    public function setBadgex($badge) 
    { 
        $this->onPropertySet('badgex',$badge); 
    
        if (!$this->badge) $this->onPropertySet('badge',$badge); 
    }
    // Calc based on cert date
    public function getExperience($asOf = null)
    {
        if (!$this->dateFirstCertified) return null;
        
        if (!$asOf) $asOf = new \DateTime();
            
        $interval = $asOf->diff($this->dateFirstCertified);
        
        $years = $interval->format('%y');
        
        return $years;
    }
}
?>
