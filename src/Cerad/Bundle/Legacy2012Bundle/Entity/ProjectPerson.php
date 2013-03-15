<?php
namespace Cerad\Bundle\Legacy2012Bundle\Entity;

class ProjectPerson extends BaseEntity
{
    protected $id;

    protected $project;

    protected $person;
  
    protected $status = 'Active';

    public function getId() { return $this->id; }
    
    public function setPerson ($person)  { $this->onObjectPropertySet('person', $person);  }
    public function setProject($project) { $this->onObjectPropertySet('project',$project); }
    public function setStatus ($status)  { $this->onScalerPropertySet('status', $status);  }

    public function getStatus()  { return $this->status;  }
    public function getProject() { return $this->project; }
    public function getPerson()  { return $this->person;  }
    
    public function getPlans()
    {
        return $this->get('plans',array());
    }    
}