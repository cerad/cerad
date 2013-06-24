<?php
namespace Cerad\Bundle\GameBundle\Entity;

/* =================================================================
 * For 2012 I used org (short for organiziation) for ayso regions
 * For 2016 I renamed this to league
 * AYSO Region, Area, Section, National
 * HASL - Huntsville Adult Soccer League
 * NASL - North Alabama Soccer League
 * High School Teams (Varsity JV etc)
 * HFC - Huntsville Futbal Club
 * VFC - Valley Futbal Club
 * USSF Alabama
 * USSF Tennesse
 * NFHS Alabama
 * NFHS Tennesse
 * 
 * Still not a real good name
 */

class League extends BaseEntity
{
    protected $id; 
    
    protected $parent = null;
    
    protected $fed;   // TBA - Federation AYSO USSF etc
    protected $role;  // TBA - AYSO Region, Area, Section
 
    protected $desc1 = null;

    protected $desc2 = null;

    protected $city  = null;

    protected $state = null;

    protected $status = 'Active';

    protected $datax = null;
    
    public function getId()      { return $this->id;     }
    public function getParent()  { return $this->parent; }
    public function getDesc1()   { return $this->desc1;  }
    public function getDesc2()   { return $this->desc2;  }
    public function getCity()    { return $this->city;   }
    public function getState()   { return $this->state;  }
    public function getStatus()  { return $this->status; }
    
    public function setId    ($id)     { return $this->onPropertySet('id',    $id);     }
    public function setParent($parent) { return $this->onPropertySet('parent',$parent); }
    public function setDesc1 ($desc1)  { return $this->onPropertySet('desc1', $desc1);  }
    public function setDesc2 ($desc2)  { return $this->onPropertySet('desc2', $desc2);  }
    public function setCity  ($city)   { return $this->onPropertySet('city',  $city);   }
    public function setState ($state)  { return $this->onPropertySet('state', $state);  }
    public function setStatus($status) { return $this->onPropertySet('status',$status); }
   
    public function getDesc3()
    {
        return substr($this->id,4) . ' ' . $this->city;
    }

}

?>
