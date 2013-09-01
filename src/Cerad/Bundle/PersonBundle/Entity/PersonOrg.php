<?php
namespace Cerad\Bundle\PersonBundle\Entity;

class PersonOrg extends BaseEntity
{
    const RoleState   = 'State';
    const RoleRegion  = 'Region';
    const RolePrimary = 'Primary';
    
    protected $id;
    protected $role;       // Primary, Region, State
    protected $orgId;      // AYSOR0894, id only no relation
    protected $identifier; // Person Identifier AYSOV12341234, relation and id
    
    protected $memYear;    // FS2012 etc
    protected $memLast;
    protected $memFirst;   // Registered or whatever
    protected $memExpires;
    
    // Probably want two objects
    protected $bcYear;
    protected $bcLast;
    protected $bcFirst;
    protected $bcExpires;
    
    protected $status   = 'Active'; // Active means all is well, Checking for needs to be checked
    protected $verified = 'No';     // Active means all is well, Checking for needs to be checked
    
    /* =================================================================
     * Accessors
     */
    public function getId        () { return $this->id;         }
    public function getRole      () { return $this->role;       }
    public function getOrgId     () { return $this->orgId;      }
    public function getStatus    () { return $this->status;     }
    public function getVerified  () { return $this->verified;   }
    public function getIdentifier() { return $this->identifier; }
    
    public function getMemYear   () { return $this->memYear;    }
    public function getMemLast   () { return $this->memLast;    }
    public function getMemFirst  () { return $this->memFirst;   }
    public function getMemExpires() { return $this->memExpires; }
    
    public function getBcYear    () { return $this->bcYear;     }
    public function getBcLast    () { return $this->bcLast;     }
    public function getBcFirst   () { return $this->bcFirst;    }
    public function getBcExpires () { return $this->bcExpires;  }
             
    public function setRole      ($value) { $this->onPropertySet('role',      $value); }
    public function setOrgId     ($value) { $this->onPropertySet('orgId',     $value); }
    public function setStatus    ($value) { $this->onPropertySet('status',    $value); }
    public function setVerified  ($value) { $this->onPropertySet('verified',  $value); }  
    public function setIdentifier($value) { $this->onPropertySet('identifier',$value); }
    
    public function setMemYear   ($value) { $this->onPropertySet('memYear',   $value); }
    public function setMemLast   ($value) { $this->onPropertySet('memLast',   $value); }
    public function setMemFirst  ($value) { $this->onPropertySet('memFirst',  $value); }
    public function setMemExpires($value) { $this->onPropertySet('memExpires',$value); }
    
    public function setBcYear    ($value) { $this->onPropertySet('bcYear',    $value); }
    public function setBcLast    ($value) { $this->onPropertySet('bcLast',    $value); }
    public function setBcFirst   ($value) { $this->onPropertySet('bcFirst',   $value); }
    public function setBcExpires ($value) { $this->onPropertySet('bcExpires', $value); }
}
?>
