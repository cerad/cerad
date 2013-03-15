<?php
namespace Zayso\CoreBundle\Entity;

class ProjectField extends BaseEntity
{
    protected $id;

    protected $project;
  
    protected $key1 = null;
  
    protected $venue = null;
    
    protected $status = 'Active';

    public function getId     () { return $this->id;      }
    public function getKey    () { return $this->key1;    }
    public function getKey1   () { return $this->key1;    }
    public function getDesc   () { return $this->key1;    }
    public function getVenue  () { return $this->venue;  }
    public function getStatus () { return $this->status;  }
    public function getProject() { return $this->project; }

    public function setKey    ($key)     { $this->onScalerPropertySet('key1',   $key);     }
    public function setVenue  ($venue)   { $this->onScalerPropertySet('venue',  $venue); }
    public function setStatus ($status)  { $this->onScalerPropertySet('status', $status);  }
    public function setProject($project) { $this->onObjectPropertySet('project',$project); }

}