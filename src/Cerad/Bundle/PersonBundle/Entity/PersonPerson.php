<?php
namespace Cerad\Bundle\PersonBundle\Entity;

/* =================================================
 * One person (the master) can have some controll over their slaves
 * For example, the master can sign slaves up for games so they slaves will not need an account
 * The master should also be notified when changes will impact their slaves
 */
class PersonPerson extends BaseEntity
{
    protected $id;
    
    protected $master;
    protected $slave;
    
    const RolePrimary = 'Primary'; // Each person relates to himself?
    const RoleFamily  = 'Family';  // John Sloan and his 3 (4?) family referees
    const RolePeer    = 'Peer';    // Referee teams formed for tournaments
    
    protected $role     = self::RolePrimary;
    protected $status   = 'Active';
    protected $verified = 'No';
    
    protected $project;  // NULL for families, non-null for tournament specific groupings
    
    public function getId      () { return $this->id;       }
    public function getRole    () { return $this->role;     }
    public function getMaster  () { return $this->master;   }
    public function getSlave   () { return $this->slave;    }
    public function getStatus  () { return $this->status;   }
    public function getVerified() { return $this->verified; }
    
    public function setRole    ($value) { $this->onPropertySet('role',    $value); }
    public function setMaster  ($value) { $this->onPropertySet('master',  $value); }
    public function setSlave   ($value) { $this->onPropertySet('slave',   $value); }
    public function setStatus  ($value) { $this->onPropertySet('status',  $value); }
    public function setVerified($value) { $this->onPropertySet('verified',$value); }
    
    public function isPrimary()
    {
        return $this->role == self::RolePrimary ? true : false;
    }
    public function isFamily()
    {
        return $this->role == self::RoleFamily ? true : false;
    }
    public function isPeer()
    {
        return $this->role == self::RolePeer ? true : false;
    }
}
?>
