<?php
namespace Cerad\Bundle\PersonBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class PersonIdentifier extends BaseEntity
{   
    const RoleAYSOV = 'AYSOV';
    const RoleAYSOP = 'AYSOP';
    const RoleUSSFC = 'USSFC';
    
    const RoleAYSOVolunteer  = 'AYSOV';
    const RoleAYSOPlayer     = 'AYSOP';
    const RoleUSSFContractor = 'USSFC';
    
    protected $id;
    protected $role;
    protected $person;
    protected $status   = 'Active';
    protected $verified = 'No';
    
    protected $orgs;
    protected $certs;
    
    public function __construct()
    {
        $this->orgs  = new ArrayCollection();
        $this->certs = new ArrayCollection();
    }
    public function getId      () { return $this->id;       }
    public function getRole    () { return $this->role;     }
    public function getPerson  () { return $this->person;   }
    public function getStatus  () { return $this->status;   }
    public function getVerified() { return $this->verified; }
    
    public function setId      ($value) { $this->onPropertySet('id',      $value); }
    public function setRole    ($value) { $this->onPropertySet('role',    $value); }
    public function setPerson  ($value) { $this->onPropertySet('person',  $value); }
    public function setStatus  ($value) { $this->onPropertySet('status',  $value); }
    public function setVerified($value) { $this->onPropertySet('verified',$value); }
    
    /* ====================================================
     * Certification
     */
    public function addCert($item)
    {
        $role = $item->getRole();
        foreach($this->certs as $itemx)
        {
            if ($itemx->getRole() == $role) return $this;
        }
        $this->certs[] = $item;
        $item->setIdentifier($this);
        $this->onPropertyChanged('certs');
    }
    public function getCerts() { return $this->certs; }
    
    public function getCert($role,$autoCreate = true)
    {
        foreach($this->certs as $item)
        {
            if ($item->getRole() == $role) return $item;
        }
        if (!$autoCreate) return null;
        
        $item = new PersonCert();
        $item->setRole($role);
        $this->addCert($item);
        return $item;
    }
    public function getCertReferee($autoCreate = true)
    {
        return $this->getCert(PersonCert::RoleReferee,$autoCreate);
    }
    // Keep forms happy
    public function setCertReferee($value) { return $this; }
    
    /* ====================================================
     * Organizations
     */
    public function addOrg($item)
    {
        $role = $item->getRole();
        foreach($this->orgs as $itemx)
        {
            if ($itemx->getRole() == $role) return $this;
        }
        $this->orgs[] = $item;
        $item->setIdentifier($this);
        $this->onPropertyChanged('orgs');
    }
    public function getOrgs() { return $this->orgs; }
    
    public function getOrg($role = null, $autoCreate = true)
    {
        /* ===========================
         * Suport mapping org role agains my rolw
         */
        if (!$role)
        {
            switch($this->role)
            {
                case self::RoleAYSOV : $role = PersonOrg::RoleRegion; break;
                case self::RoleAYSOP : $role = PersonOrg::RoleRegion; break;
                case self::RoleUSSFC : $role = PersonOrg::RoleState;  break;
                default              : $role = PersonOrg::RoleDefault;
            }
        }
        foreach($this->orgs as $item)
        {
            if ($item->getRole() == $role) return $item;
        }
        if (!$autoCreate) return null;
        
        $item = new PersonOrg();
        $item->setRole($role);
        $this->addOrg ($item);
        return $item;
    }
    public function getOrgState($autoCreate = true)
    {
        return $this->getOrg(PersonOrg::RoleState,$autoCreate);
    }
    public function getOrgRegion($autoCreate = true)
    {
        return $this->getOrg(PersonOrg::RoleRegion,$autoCreate);
    }
    // Keep forms happy
    public function setOrgState ($value) { return $this; }
    public function setOrgRegion($value) { return $this; }
}
?>
