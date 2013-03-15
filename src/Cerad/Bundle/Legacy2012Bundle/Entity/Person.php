<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Person extends BaseEntity
{
    protected $id;

    protected $firstName = null;

    protected $lastName  = null;

    protected $nickName  = null;
    
    protected $gender    = null;
    
    protected $dob       = null;

    protected $email     = null;

    protected $cellPhone = null;

    protected $verified  = 'No';

    protected $status    = 'Active';
    
    protected $registeredPersons;
    
    protected $accounts;

    protected $personPersons;
    
    protected $projectPersons;
    
    protected $teamRels;
    
    protected $gameRels;

    public function getGameRels() { return $this->gameRels; }

    public function __construct()
    {
        $this->teamRels          = new ArrayCollection();
        $this->gameRels          = new ArrayCollection();
        $this->accounts          = new ArrayCollection();
        $this->personPersons     = new ArrayCollection();
        $this->projectPersons    = new ArrayCollection();
        $this->registeredPersons = new ArrayCollection();
    }
    public function getPersonPersons() 
    { 
        // Add a sort for Primary Family Peer
        return $this->personPersons; 
    }
    public function getAccounts() 
    { 
        return $this->accounts; 
    }
    
    public function getGameRelsForProject($projectId)
    {
        $gameRelsx = array();
        foreach($this->gameRels as $gameRel)
        {
            $game = $gameRel->getEvent();
            if ($game)
            {
                if ($game->getProject()->getId() == $projectId) $gameRelsx[] = $gameRel;
            }
        }
        return $gameRelsx;
    }
    public function addProjectPerson($projectPerson)
    {
        // Prevent dups
        /*
        $projectId = $projectPerson->getProject()->getId();
        foreach($this->projectPersons as $projectPerson)
        {
            if ($projectPerson->getProject()->getId() == $projectId) return;
        }*/
        // Really should not happen
        if (!$projectPerson) return;
        
        // Filter out those with a null for project
        // Did this because had cascade=persist
        if (!$projectPerson->getProject()) return;
        
        $this->projectPersons[] = $projectPerson;
    }
    public function getProjectPerson($projectId)
    {
        foreach($this->projectPersons as $projectPerson)
        {
            if ($projectPerson->getProject()->getId() == $projectId) return $projectPerson;
        }
        return null;
    }
    public function getProjectPersons() { return $this->projectPersons; }
    public function clearProjectPersons() { $this->projectPersons = new ArrayCollection(); }
    
    /* =====================================================================
     * This is called when one and only one project has been loaded
     * In most cases, it will be the current project
     * If one is not found then a fake one is created
     * 
     * It is interesting that the form does not seem to call setCurrentProjectPeron
     * Rather it calls get then sets the project for entity
     */
    protected $currentProjectPersonTemp = null;
    
    public function getCurrentProjectPerson()
    {
        if (count($this->projectPersons) == 1) return $this->projectPersons[0];
        
        if (!$this->currentProjectPersonTemp) 
        {
            $this->currentProjectPersonTemp = new ProjectPerson();
            $this->currentProjectPersonTemp->setPerson($this);
        }
        return $this->currentProjectPersonTemp;
    }
    public function addRegisteredPerson($reg)
    {
        $this->registeredPersons[$reg->getRegType()] = $reg;
    }
    public function getRegisteredPersons() { return $this->registeredPersons; }

    public function getPersonName()
    {
        $fname = $this->getFirstName();
        $lname = $this->getLastName();
        $nname = $this->getNickName();

        if ($nname) $name =  $nname . ' ' . $lname;
        else        $name =  $fname . ' ' . $lname;

        return $name;
    }
    public function getName() { return $this->getPersonName(); }
    
    /* ======================================================================
     * Standard getter/setters
     */
    public function getId       () { return $this->id; }
    public function getDob      () { return $this->dob; }
    public function getEmail    () { return $this->email;   }

    public function getStatus   () { return $this->status; }
    public function getGender   () { return $this->gender; }
    public function getVerified () { return $this->verified;      }
    public function getLastName () { return $this->lastName;  }
    public function getNickName () { return $this->nickName;  }
    public function getFirstName() { return $this->firstName; }
    public function getCellPhone() { return $this->cellPhone; }

    
    public function setDob      ($value) { $this->onScalerPropertySet('dob',      $value); }
    public function setEmail    ($value) { $this->onScalerPropertySet('email',    $value); }
    public function setGender   ($value) { $this->onScalerPropertySet('gender',   $value); }
    public function setStatus   ($value) { $this->onScalerPropertySet('status',   $value); }
    public function setVerified ($value) { $this->onScalerPropertySet('verified', $value); }
    public function setLastName ($value) { $this->onScalerPropertySet('lastName', $value); }
    public function setNickName ($value) { $this->onScalerPropertySet('nickName', $value); }
    public function setFirstName($value) { $this->onScalerPropertySet('firstName',$value); }
    public function setCellPhone($value) { $this->onScalerPropertySet('cellPhone',$value); }

    // ==========================================================
    // Seems to work okay
    protected $regAYSOVTemp = null;
    
    public function getRegAYSOV()
    {
      //$this->getRegItem('Zayso\CoreBundle\Entity\PersonRegAYSOV');
        
        if (isset($this->registeredPersons[PersonRegAYSOV::REGTYPE])) 
        {
            return $this->registeredPersons[PersonRegAYSOV::REGTYPE];
        }
        
        if ($this->regAYSOVTemp) return $this->regAYSOVTemp;
        
        $this->regAYSOVTemp = new PersonRegAYSOV();
        $this->regAYSOVTemp->setPerson ($this);
        
        return $this->regAYSOVTemp;
    }
    protected $regUSSFTemp = null;
    
    public function getRegUSSF()
    {
        if (isset($this->registeredPersons[PersonRegUSSF::REGTYPE])) 
        {
            return $this->registeredPersons[PersonRegUSSF::REGTYPE];
        }
        
        if ($this->regUSSFTemp) return $this->regUSSFTemp;
        
        $this->regUSSFTemp = new PersonRegUSSF();
        $this->regUSSFTemp->setPerson($this);
        
        return $this->regUSSFTemp;
    }
    
    // This should work just fine, classname needs to be FQN
    protected function getRegItem($className)
    {
        if (isset($this->registeredPersons[PersonRegAYSOV::REGTYPE])) 
        {
            return $this->registeredPersons[PersonRegAYSOV::REGTYPE];
        }
        $type = $className::REGTYPE;
        die('Get type ' . $type);
    }
    /* ========================================================================
     * Team relations
     */
    public function getTeamRels() { return $this->teamRels; }
    
    public function addTeamRel($rel)
    {
        $this->teamRels[$rel->getId()] = $rel;
    }
    public function removeTeamRel($rel)
    {
        unset($this->teamRels[$rel->getId()]);
    }
}
