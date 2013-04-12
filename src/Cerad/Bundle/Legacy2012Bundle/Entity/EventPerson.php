<?php

namespace Cerad\Bundle\Legacy2012Bundle\Entity;

/* =======================================
 * Assigned  - Pending  - Published - Accepted
 * Requested - Approved - Published - Accepted
 * Changed   - Pending  - Published - Accepted
 *
 * On site
 * Performed
 * 
 */
class EventPerson extends BaseEntity
{   
    const TypeCR  = 'CR';
    const TypeCR2 = 'CR2';
    
    const Type4th = '4TH';
    const TypeObs = 'OBS';
    
    const TypeREF  = 'REF';
    const TypeREF1 = 'REF1';
    const TypeREF2 = 'REF2';
    
    const TypeAR1  = 'AR1';
    const TypeAR2  = 'AR2';

    static public $typeDescs = array
    (
        self::TypeCR  => 'Center',
        self::TypeCR2 => 'Center 2', // Dual

        self::TypeAR1 => 'Assistant 1', // DSC
        self::TypeAR2 => 'Assistant 2',
        
        self::TypeREF  => 'Referee',
        self::TypeREF1 => 'Referee 1', // Futsal
        self::TypeREF2 => 'Referee 2',

        self::Type4th => '4th Official',
        self::TypeObs => 'Observer',

    );
    protected $id = 0;
    
    protected $project = null;
    
    protected $event = null;
    
    protected $person = null;
    
    protected $type = null;

    protected $sort = 0;

    protected $protected = false;

    protected $state = null;

    protected $userModified = null;
    
    protected $adminModified = null;

    protected $datax = null;

    
    /* =========================================================
     * Custom code
     */
    
    /* =========================================================
     * Standard getter/setter
     */    
    public function getId()    { return $this->id; }
    public function setId($id) { $this->id = $id; }
    
    public function setProject($project) { $this->onObjectPropertySet('project', $project); }
    public function getProject()         { return $this->project;     }

    public function setGame($event) { $this->setEvent($event); }
    public function getGame()       { return $this->event;     }
    
    public function setEvent($event) { $this->onObjectPropertySet('event', $event); }
    public function getEvent()       { return $this->event;  }
    
    public function setTypeAsCR  () { $this->setType(self::TypeCR );  }
    public function setTypeAsCR2 () { $this->setType(self::TypeCR2);  }
    public function setTypeAsAR1 () { $this->setType(self::TypeAR1);  }
    public function setTypeAsAR2 () { $this->setType(self::TypeAR2);  }
    public function setTypeAsRef () { $this->setType(self::TypeREF);  }
    public function setTypeAsRef1() { $this->setType(self::TypeREF1); }
    public function setTypeAsRef2() { $this->setType(self::TypeREF2); }
    public function setTypeAs4th () { $this->setType(self::Type4th);  }
    public function setTypeAsObs () { $this->setType(self::TypeObs);  }
    
    public function setType($type) 
    {
        $this->onScalerPropertySet('type', $type);
        switch($type)
        {
            case self::TypeCR :  $this->sort = 11; break;
            case self::TypeCR2:  $this->sort = 12; break;
            case self::TypeREF:  $this->sort = 13; break;
            case self::TypeREF1: $this->sort = 14; break;
            case self::TypeREF2: $this->sort = 15; break;
            case self::TypeAR1:  $this->sort = 21; break;
            case self::TypeAR2:  $this->sort = 22; break;
            case self::Type4th:  $this->sort = 31; break;
            case self::TypeObs:  $this->sort = 41; break;
            default:             $this->sort = 99;
        }
    }
    public function getType()      { return $this->type;  }
    public function getTypeDesc()
    {
        $type = $this->type;
        if (isset(self::$typeDescs[$type])) return self::$typeDescs[$type];
        return $type;
    }
    public function getTypeDescs() { return self::$typeDescs; }
    
    public function setState($state) { $this->onScalerPropertySet('state', $state); }
    public function getState()       { return $this->state;  }

    public function setPerson($person) { $this->onObjectPropertySet('person', $person); }
    public function getPerson()        { return $this->person;  }
    
    protected $personz = null;
    public function getPersonz()       
    { 
        if ( $this->person) return $this->person;
        if (!$this->personz)
        {
            $this->personz = new Person();
        }
        return $this->personz;  
    }

    public function getPersonName()
    {
        if (!$this->person) return null;
        return $this->person->getPersonName();
    }
    public function getPersonId()
    {
        if (!$this->person) return null;
        return $this->person->getId();
    }
    protected $personIdx = null;
    
    public function setPersonIdx($personId)
    {
        $this->personIdx = $personId;
    }
    public function getPersonIdx() 
    {
        if ($this->personIdx) return $this->personIdx;
        return $this->getPersonId(); 
    }
    
    // Real hack here, want to perserve state for form processing
    protected $statex = null;
    
    public function setStatex($state) { $this->statex = $state; }
    public function getStatex()       
    { 
        if ($this->statex) return $this->statex;
        return $this->state;
    }
    
    public function setProtected($value) { $this->protected = $value; }
    public function getProtected() { return $this->protected; }
    public function isProtected () { return $this->protected ? true : false; }
    
    // Date time stuff
    public function getUserModified()  { return $this->userModified; }
    public function getAdminModified() { return $this->adminModified; }
    
    public function setUserModified($dt = null) 
    {
        if (!$dt) $dt = new \DateTime('now');
        $this->onObjectPropertySetx('userModified', $dt);
    }
    public function setAdminModified($dt = null) 
    {   
        if (!$dt) $dt = new \DateTime('now');
        $this->onObjectPropertySetx('adminModified', $dt);
    }    
}
?>
