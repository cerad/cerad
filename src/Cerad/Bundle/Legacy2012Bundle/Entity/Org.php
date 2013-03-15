<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

class Org extends BaseEntity
{
    protected $id;

    protected $parent = null;

    protected $desc1 = null;

    protected $desc2 = null;

    protected $city  = null;

    protected $state = null;

    protected $status = 'Active';

    public function getDesc3()
    {
        return substr($this->id,4) . ' ' . $this->city;
    }

    public function setId($id)         { $this->onScalerPropertySet('id',$id); }
    public function getId()            { return $this->id; }
    
    public function setParent($parent) { $this->onObjectPropertySet('parent',$parent); }
    public function getParent()        { return $this->parent; }

    public function setDesc1($desc1)   { $this->onScalerPropertySet('desc1',$desc1); }
    public function getDesc1()         { return $this->desc1; }
    
    public function setDesc2($desc2)   { $this->onScalerPropertySet('desc2',$desc2); }
    public function getDesc2()         { return $this->desc2; }
    
    public function setCity($city)     { $this->onScalerPropertySet('city',$city); }
    public function getCity()          { return $this->city; }
    
    public function setState($state)   { $this->onScalerPropertySet('state',$state); }
    public function getState()         { return $this->state; }

    public function setStatus($status) { $this->onScalerPropertySet('status',$status); }
    public function getStatus()        { return $this->status; }
}