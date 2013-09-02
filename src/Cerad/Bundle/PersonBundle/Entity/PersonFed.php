<?php
namespace Cerad\Bundle\PersonBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class PersonFed extends BaseEntity
{   
    const FedAYSO = 'AYSO';
    const FedUSSF = 'USSF';
    const FedNFHS = 'NFHS';
    
    const RoleVolunteer  = 'Volunteer';
    const RolePlayer     = 'Player';
    const RoleContractor = 'Contractor';
    const RoleOfficial   = 'Official';
    
    protected $id;
    protected $role;
    protected $fedId;
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
    public function getFedId   () { return $this->fedId;    }
    public function getPerson  () { return $this->person;   }
    public function getStatus  () { return $this->status;   }
    public function getVerified() { return $this->verified; }
    
    public function setId      ($value) { $this->onPropertySet('id',      $value); }
    public function setRole    ($value) { $this->onPropertySet('role',    $value); }
    public function setFedId   ($value) { $this->onPropertySet('fedId',   $value); }
    public function setPerson  ($value) { $this->onPropertySet('person',  $value); }
    public function setStatus  ($value) { $this->onPropertySet('status',  $value); }
    public function setVerified($value) { $this->onPropertySet('verified',$value); }
    
    /* ====================================================
     * Certification
     */
    public function newCert() { return new PersonCert(); }

    public function addCert($item)
    {
        $role = $item->getRole();
        foreach($this->certs as $itemx)
        {
            if ($itemx->getRole() == $role) return $this;
        }
        $this->certs[] = $item;
        $item->setFed($this);
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
    public function newOrg() { return new PersonOrg(); }
    
    public function addOrg($item)
    {
        $role = $item->getRole();
        foreach($this->orgs as $itemx)
        {
            if ($itemx->getRole() == $role) return $this;
        }
        $this->orgs[] = $item;
        $item->setFed($this);
        $this->onPropertyChanged('orgs');
    }
    public function getOrgs() { return $this->orgs; }
    
    public function getOrg($role = null, $autoCreate = true)
    {
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
