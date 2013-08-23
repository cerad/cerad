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
    protected $identifiers;
    
    public function __construct()
    {
        $this->certs       = new ArrayCollection();
        $this->plans       = new ArrayCollection();
        $this->leagues     = new ArrayCollection();
        $this->persons     = new ArrayCollection();
        $this->identifiers = new ArrayCollection();
       
        $this->id = Guid::gen();
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
        
        if (!$asOf) $asOf = new \DateTime();
            
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
    
    public function getCert($fed,$role,$autoCreate = true)
    {
         foreach($this->certs as $item)
        {
            if (($item->getFed() == $fed) && ($item->getRole() == $role))
            {
                return $item;
            }
        }
        if (!$autoCreate) return null;
        
        $item = new PersonCert();
        $item->setFed ($fed);
        $item->setRole($role);
        $this->addCert($item);
        return $item;
    }
    public function getCertRefereeUSSF($autoCreate = true)
    {
        return $this->getCert(PersonCert::FedUSSF,PersonCert::RoleReferee,$autoCreate);
    }
    public function getCertRefereeAYSO($autoCreate = true)
    {
        return $this->getCert(PersonCert::FedAYSO,PersonCert::RoleReferee,$autoCreate);
    }
    public function getCertUSSFReferee($autoCreate = true)
    {
        return $this->getCert(PersonCert::FedUSSF,PersonCert::RoleReferee,$autoCreate);
    }
    public function getCertAYSOReferee($autoCreate = true)
    {
        return $this->getCert(PersonCert::FedAYSO,PersonCert::RoleReferee,$autoCreate);
    }
    // Keep forms happy
    public function setCertRefereeAYSO($value) { return $this; }
    public function setCertRefereeUSSF($value) { return $this; }
    public function setCertAYSOReferee($value) { return $this; }
    public function setCertUSSFReferee($value) { return $this; }
    
    /* ====================================================
     * Leagues
     */
    public function addLeague($league)
    {
        $this->leagues[] = $league;
        $league->setPerson($this);
    }
    public function getLeagues() { return $this->leagues; }
    
    public function getLeague($fed,$role,$autoCreate = true)
    {
         foreach($this->leagues as $item)
        {
            if (($item->getFed() == $fed) && ($item->getRole() == $role))
            {
                return $item;
            }
        }
        if (!$autoCreate) return null;
        
        $item = new PersonLeague();
        $item->setFed   ($fed);
        $item->setRole  ($role);
        $this->addLeague($item);
        return $item;
    }
    public function getVolunteerAYSO($autoCreate = true)
    {
        return $this->getLeague(PersonLeague::FedAYSO,PersonLeague::RoleVolunteer,$autoCreate);
    }
    public function getLeagueAYSOVolunteer($autoCreate = true)
    {
        return $this->getLeague(PersonLeague::FedAYSO,PersonLeague::RoleVolunteer,$autoCreate);
    }
    // Need for forms
    public function setVolunteerAYSO($value) { return $this; }
    public function setLeagueAYSOVolunteer($value) { return $this; }
    
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
    public function getPlans() { return $this->plans; }
    
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
    /* ====================================================
     * Unique identifiers
     */
    public function addIdentifier($identifier)
    {
        $this->identifiers[] = $identifier;
        $identifier->setPerson($this);
    }
    public function getIdentifiers() { return $this->identifiers; }
    
    public function getIdentifier($identifierKey)
    {
        if (is_object($identifierKey)) $identifierKey = $identifierKey->getKey();
        
        foreach($this->identifiers as $identifier)
        {
            if ($identifier->getKey() == $identifierKey)
            {
                return $identifier;
            }
        }
        return null;
    }
    /* ========================================================
     * Generate and set the person's full name
     */
    public function genName()
    {
        if ($this->nickName) $name = $this->nickName.  ' ' . $this->lastName;
        else                 $name = $this->firstName. ' ' . $this->lastName;
        
        $this->setName($name);
        return $this;
   }
   /* ==========================================================
    * In many cases we are only interested on one league
    * Cleverly call this leaguex
    */
   public function setLeaguex($league)
   {
       $this->leaguex = $league;
   }
   public function getLeaguex($autoCreate = true)
   {
       if ($this->leaguex) return $this->leaguex;
       
       if (!$autoCreate) return null;
       
       $this->leaguex = new League();
       
       return $this->leaguex;
       
   }
}
