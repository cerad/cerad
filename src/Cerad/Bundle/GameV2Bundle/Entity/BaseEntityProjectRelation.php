<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

/* =================================================
 * Use this to connect an entity to a project
 */
class BaseEntityProjectRelation extends BaseEntity
{
    // Maybe try a composite key?
    protected $id;
    
    protected $project;
    protected $entity;
    
    protected $name;   // Overide entities name on a project basis
    protected $sort;   // Sort within a project
    
    protected $status = 'Active';  // Just Because
    
    public function getId()      { return $this->id;      }
    public function getName()    { return $this->name;    }
    public function getSort()    { return $this->sort;    }
    public function getStatus()  { return $this->status;  }
    public function getEntity()  { return $this->entity;  }
    public function getProject() { return $this->project; }
    
    public function setId     ($value) { $this->onPropertySet('id',    $value); }
    public function setName   ($value) { $this->onPropertySet('name',  $value); }
    public function setSort   ($value) { $this->onPropertySet('sort',  $value); }
    public function setStatus ($value) { $this->onPropertySet('status',$value); }
    
    public function setProject($value) { $this->onPropertySet('project',$value); }
    
    public function setEntity($entity) 
    { 
        $this->onPropertySet('entity', $entity);
        if ($entity)
        {
            $this->onPropertySet('name', $entity->getName());
        }
    }
}
?>
