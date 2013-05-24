<?php
namespace Cerad\Bundle\PersonBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Person extends BaseEntity
{
    const GenderMale    = 'M';
    const GenderFemale  = 'F';
    const GenderUnknown = 'U';
    
    protected $id;
    protected $idx; // Just to help with legacy import
    
    protected $name;
    
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
    protected $address;
    protected $city;
    protected $state;
    protected $zipcode;
    
    protected $verified  = 'No';
    protected $status    = 'Active';
    
    protected $plans;
    protected $certs;
    protected $leagues;
    protected $persons;
    
    public function __construct()
    {
        $this->certs   = new ArrayCollection();
        $this->plans   = new ArrayCollection();
        $this->leagues = new ArrayCollection();
        $this->persons = new ArrayCollection();
        
        // Simple 32 char string, not really a guid but oh well
        $this->id = strtoupper(md5(uniqid('zayso',true)));
    }
    /* ======================================================================
     * Standard getter/setters
     */
    public function getId       () { return $this->id;  }
    public function getIdx      () { return $this->idx; }
    public function getDob      () { return $this->dob; }
    public function getName     () { return $this->name;   }
    public function getEmail    () { return $this->email;  }
    public function getPhone    () { return $this->phone;  }
    
    public function getCity     () { return $this->city;   }
    public function getState    () { return $this->state;   }

    public function getStatus   () { return $this->status; }
    public function getGender   () { return $this->gender; }
    public function getVerified () { return $this->verified;      }
    public function getLastName () { return $this->lastName;  }
    public function getNickName () { return $this->nickName;  }
    public function getFirstName() { return $this->firstName; }

    public function setIdx      ($value) { $this->onPropertySet('idx',      $value); }
    public function setDob      ($value) { $this->onPropertySet('dob',      $value); }
    public function setName     ($value) { $this->onPropertySet('name',     $value); }
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
    
    // Really sets the dob
    public function setAge($age)
    {
        if (!$age)              return;
        if ($this->age == $age) return;
        
        $year = 2013 - $age;
        $dt = new \DateTime($year . '-01-01 00:00');
        
        $this->setDob($dt);
        $this->age = $age;
    }
    // Should return age as of some date
    public function getAge($asOf = null)
    {
        if (!$this->dob) return null;
        
        if (!$asof) $asOf = new \DateTime();
            
        $interval = $asOf->diff($this->dob);
        
        $years = $interval->format('%y');
        
        return $years;
    }
    /* ====================================================
     * Certification
     */
    public function addCert($cert)
    {
        $this->certs[] = $cert;
        $cert->setPerson($this);
    }
    public function getCerts() { return $this->certs; }
    
    public function getCertRefereeUSSF()
    {
        foreach($this->certs as $cert)
        {
            if (($cert->getFed() == PersonCert::FedUSSF) && ($cert->getRole() == PersonCert::RoleReferee))
            {
                return $cert;
            }
        }
        return null;
    }
    public function getCertRefereeAYSO()
    {
        foreach($this->certs as $cert)
        {
            if (($cert->getFed() == PersonCert::FedAYSO) && ($cert->getRole() == PersonCert::RoleReferee))
            {
                return $cert;
            }
        }
        return PersonCert::createRefereeAYSO();
    }
    /* ====================================================
     * Leagues
     */
    public function addLeague($league)
    {
        $this->leagues[] = $league;
        $league->setPerson($this);
    }
    public function getLeagues() { return $this->leagues; }
    
    public function getVolunteerAYSO()
    {
        foreach($this->leagues as $league)
        {
            if (($league->getFed() == PersonLeague::FedAYSO) && ($league->getRole() == PersonLeague::RoleVolunteer))
            {
                return $league;
            }
        }
        return PersonLeague::createVolunteerAYSO();
    }
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
    /* ====================================================
     * Project Plans
     */
    public function addPlan($plan)
    {
        $this->plans[] = $plan;
        $plan->setPerson($this);
    }
    public function getPlans() { return $this->plans(); }
    
    public function getPlan($projectKey)
    {
        if (is_object($projectKey)) $projectKey = $projectKey->getKey();
        
        foreach($this->plans as $plan)
        {
            if ($plan->getProjectKey() == $projectKey)
            {
                return $plan;
            }
        }
        return null;
    }
}
