<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Account extends BaseEntity
{
    protected $id;

    protected $userName = null;

    protected $userPass  = null;

    protected $status = 'Active';
    
    protected $reset = null;
    
    protected $person;
    
    protected $openids;

    public function __construct()
    {
        $this->openids = new ArrayCollection();
    }

    public function getId()    { return $this->id; }
    
    public function setUserName($userName) { $this->onScalerPropertySet('userName',$userName); }
    public function getUserName()          { return $this->userName; }

    public function setUserPass($userPass) { $this->onScalerPropertySet('userPass',$userPass); }
    public function getUserPass()          { return $this->userPass; }

    public function setStatus($status)     { $this->onScalerPropertySet('status',$status); }
    public function getStatus()            { return $this->status; }
    
    public function setReset($reset)       { $this->onScalerPropertySet('reset',$reset); }
    public function getReset()             { return $this->reset; }
    
    public function setPerson($person)     { $this->onObjectPropertySet('person',$person); }
    
    protected $personTemp = null;
    public function getPerson()            
    { 
        if ( $this->person) return $this->person;
        if (!$this->personTemp)
        {
            $this->personTemp = new Person();
        }
        return $this->personTemp;
    }
    
    // Openid stuff
    public function getOpenids() { return $this->openids; }

  //public function addOpenid($openid) { $this->openids[] = $openid; }

  //public function clearOpenids() { $this->openids = new ArrayCollection(); }
    

}