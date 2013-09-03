<?php
namespace Cerad\Bundle\PersonBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

use Cerad\Bundle\CommonBundle\Functions\Guid;

class Person extends BaseEntity
{
    const GenderMale    = 'M';
    const GenderFemale  = 'F';
    const GenderUnknown = 'U';
    
    protected $id;
    protected $name;
    protected $note;
    
    protected $lastName;
    protected $nickName;  // Obsolete?
    
    protected $firstName;
    
    protected $gender;
    
    protected $dob; // DateTime
    protected $age;
    
    protected $email;

    protected $phone;
    protected $phoneProvider; // For texting?

    // Move these to PersonAddress?
    // Want to handle work locations and college locations
    protected $city;
    protected $state;
    protected $zipcode;
    
    protected $verified  = 'No';
    protected $status    = 'Active';
    
    protected $feds;
    protected $plans;
    protected $persons;
    
    public function __construct()
    {
        $this->id          = Guid::gen();   
        $this->feds        = new ArrayCollection();
        $this->plans       = new ArrayCollection();
        $this->persons     = new ArrayCollection();
    }
    
    /* ======================================================================
     * Standard getter/setters
     */
    public function getId       () { return $this->id;     }
    public function getDob      () { return $this->dob;    }
    public function getName     () { return $this->name;   }
    public function getNote     () { return $this->note;   }
    public function getEmail    () { return $this->email;  }
    public function getPhone    () { return $this->phone;  }
    
    public function getCity     () { return $this->city;   }
    public function getState    () { return $this->state;  }

    public function getStatus   () { return $this->status;    }
    public function getGender   () { return $this->gender;    }
    public function getVerified () { return $this->verified;  }
    public function getLastName () { return $this->lastName;  }
    public function getNickName () { return $this->nickName;  }
    public function getFirstName() { return $this->firstName; }

    public function setId       ($value) { $this->onPropertySet('id',       $value); }
    public function setDob      ($value) { $this->onPropertySet('dob',      $value); }
    public function setName     ($value) { $this->onPropertySet('name',     $value); }
    public function setNote     ($value) { $this->onPropertySet('note',     $value); }
    public function setCity     ($value) { $this->onPropertySet('city',     $value); }
    public function setState    ($value) { $this->onPropertySet('state',    $value); }
    public function setEmail    ($value) { $this->onPropertySet('email',    $value); }
    public function setPhone    ($value) { $this->onPropertySet('phone',    $value); }
    public function setGender   ($value) { $this->onPropertySet('gender',   $value); }
    public function setStatus   ($value) { $this->onPropertySet('status',   $value); }
    public function setVerified ($value) { $this->onPropertySet('verified', $value); }
    public function setLastName ($value) { $this->onPropertySet('lastName', $value); }
    public function setNickName ($value) { $this->onPropertySet('nickName', $value); }
    public function setFirstName($value) { $this->onPropertySet('firstName',$value); }
    
    /* ===========================================
     * Age with optional asOf date
     */
    public function getAge($asOf = null)
    {
        if (!$this->dob) return null;
        
        if (!$asOf) $asOf = new \DateTime();
            
        $interval = $asOf->diff($this->dob);
        
        $years = $interval->format('%y');
        
        return $years;
    }
    /* ====================================================
     * Federation
     */
    public function newFed() { return new PersonFed(); }
    
    public function addFed($item)
    {
        $fedId = $item->getFedId();
        $role  = $item->getRole();
        foreach($this->feds as $itemx)
        {
            if (($fedId == $itemx->getFedId()) && ($role == $itemx->getRole())) return $this;
        }
        $this->feds[] = $item;
        $item->setPerson($this);
        $this->onPropertyChanged('feds');
    }
    public function getFeds() { return $this->feds; }
    
    public function getFed($fedId, $role, $autoCreate = true) 
    { 
        foreach($this->feds as $item)
        {
           if (($fedId == $item->getFedId()) && ($role == $item->getRole())) return $item;
        }
        if (!$autoCreate) return null;
        
        $item = $this->newFed();
        $item->setRole ($role);
        $item->setFedId($fedId);
        $this->addFed  ($item);
        return $item;
    }
    public function getFedAYSOV($autoCreate = true) 
    { 
        return $this->getFed(PersonFed::FedAYSO, PersonFed::RoleVolunteer,  $autoCreate);
    }
    public function getFedUSSFC($autoCreate = true) 
    { 
        return $this->getFed(PersonFed::FedUSSF, PersonFed::RoleContractor, $autoCreate);
    }    
    // Need for forms
    public function setFedAYSOV($value) { return $this; }
    public function setFedUSSFC($value) { return $this; }
    
    /* ====================================================
     * Persons
     */
    public function addPerson($person)
    {
        $this->persons[] = $person;
        $person->setMaster($this);
    }
    public function getPersons() 
    { 
        return $this->persons;
        
        // Create the primary entity on the fly?
        $primaryPerson = new PersonPerson();
        $primaryPerson->setMaster($this);
        $primaryPerson->setSlave ($this);
        $primaryPerson->setRole  (PersonPerson::RolePrimary);
         
        return array_merge(array($primaryPerson,$this->persons)); 
    }
    public function getPerson($personx)
    {
        $persons = $this->getPersons();
        foreach($persons as $person)
        {
            if ($person->getSlave()->getId() == $personx->getId()) return $person;
        }
        return null;
    }
    public function getPersonPersonPrimary($autoCreate = true)
    {
        foreach($this->persons as $personPerson)
        {
            if ($personPerson->getRole() == PersonPerson::RolePrimary)
            {
                // Should only be one primary
                return $personPerson;
            }
        }
        if (!$autoCreate) return null;
            
        $personPerson = new PersonPerson();
        $personPerson->setMaster($this);
        $personPerson->setSlave ($this);
        $personPerson->setRole  (PersonPerson::RolePrimary);
            
        $this->addPerson($personPerson);
            
        return $personPerson;
    }
    /* ====================================================
     * Project Plans
     */
    public function newPlan() { return new PersonPlan(); }
    
    public function addPlan($item)
    {
        $projectId = $item->getProjectId();
        foreach($this->plans as $itemx)
        {
            if ($itemx->getProjectId() == $projectId) return $this;
        }
        $this->plans[] = $item;
        $item->setPerson($this);
        $this->onPropertyChanged('plans');
    }
    public function getPlans() { return $this->plans; }
    
    public function getPlan($project, $autoCreate = true) 
    { 
        $projectId = is_object($project) ? $project->getId() : $project;
        
        foreach($this->plans as $item)
        {
            if ($item->getProjectId() == $projectId) 
            {
                if (is_object($project)) $item->setPlanProperties($project->getPlan());
                return $item;
            }
        }
        if (!$autoCreate) return null;
        
        $item = $this->newPlan();
        $item->setProjectId($projectId);
        if (is_object($project)) $item->setPlanProperties($project->getPlan());
        $this->addPlan($item);
        return $item;
    }
}
