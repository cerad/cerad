<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

class PersonPerson extends BaseEntity
{
    protected $id;
  
    protected $person1;
    
    protected $person2;
    
    protected $relation = null;

    protected $verified = 'No';

    protected $status = 'Active';

    public function __construct()
    {
    }
    public function getId() { return $this->id; }
    
    public function setPerson1($person)    { $this->onObjectPropertySet('person1',$person); }
    public function getPerson1()           { return $this->person1; }
    
    public function setPerson2($person)    { $this->onObjectPropertySet('person2',$person); }
    public function getPerson2()           { return $this->person2; }

    public function setVerified($verified) { $this->onScalerPropertySet('verified',$verified); }
    public function getVerified()          { return $this->verified; }

    public function setStatus($status)     { $this->onScalerPropertySet('status',$status); }
    public function getStatus()            { return $this->status; }

    public function setRelation($relation) { $this->onScalerPropertySet('relation',$relation); }
    public function getRelation()          { return $this->relation; }

    public function isPrimary() { return $this->relation == 'Primary' ? true : false; }
    public function isFamily () { return $this->relation == 'Family'  ? true : false; }
    public function isPeer   () { return $this->relation == 'Peer'    ? true : false; }

    public function setAsPrimary() { $this->setRelation('Primary'); }
    public function setAsFamily () { $this->setRelation('Family' ); }
    public function setAsPeer   () { $this->setRelation('Peer'   ); }

}