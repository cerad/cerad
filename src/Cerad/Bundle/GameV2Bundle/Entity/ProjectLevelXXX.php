<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

class ProjectLevel extends BaseEntityEntity
{
    protected $project;
    protected $projectName;
    protected $projectSort;
    
    protected $level;
    protected $levelName;
    protected $levelSort;
    
    public function getEntity1()     { return $this->entity1; }
    public function getProject()     { return $this->project; }
    public function getProjectName() { return $this->name1;   }
    public function getProjectSort() { return $this->sort1;   }
    
    public function getEntity2()     { return $this->entity1; }
    public function getLevel()       { return $this->level;   }
    public function getLevelName()   { return $this->name2;   }
    public function getLevelSort()   { return $this->sort2l;  }
    
    public function setProject($entity) { return $this->setEntity1($entity); }
    public function setLevel  ($entity) { return $this->setEntity2($entity); }
    
    public function setEntity1($entity,$sort = null, $name = null) 
    { 
        $this->onPropertySet('project', $entity);
        if ($entity)
        {
            $this->onPropertySet('sort1', $sort);
            
            if ($name) $this->onPropertySet('name1', $name);
            else       $this->onPropertySet('name1', $entity->getName()); // Debug for now
        }
    }
    public function setEntity2($entity,$sort = null, $name = null) 
    { 
        $this->onPropertySet('level', $entity);
        if ($entity)
        {
            $this->onPropertySet('sort2', $sort);
            
            if ($name) $this->onPropertySet('name2', $name);
            else       $this->onPropertySet('name2', $entity->getName());   
        }
    }
}
?>
