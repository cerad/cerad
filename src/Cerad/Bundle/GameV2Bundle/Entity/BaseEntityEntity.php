<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

/* =================================================
 * Generic Entity to Entity relation class
 * 
 * So far it is working well but it's difficult to remember the item1 vs item2 stuff
 * 
 * Problem is that entity1,name1,sort1 are usedd for database mapping and find/sort commands
 * Really should be project,projectName,projectSort
 * But that reduces the effectiviness of a base class
 * 
 * Spelling the names leads to better documentation
 * However, leaving as is promotes reuse
 */
class BaseEntityEntity extends BaseEntity
{
    protected $id;
    protected $role;
    protected $status = 'Active';
    
    protected $name1;
    protected $sort1;
    protected $entity1;
    
    protected $name2;
    protected $sort2;
    protected $entity2;
    
    public function getId()      { return $this->id;      }
    public function getRole()    { return $this->role;    }
    public function getStatus()  { return $this->status;  }
    
    public function getName1()    { return $this->name1;    }
    public function getSort1()    { return $this->sort1;    }
    public function getEntity1()  { return $this->entity1;  }
    
    public function getName2()    { return $this->name2;    }
    public function getSort2()    { return $this->sort2;    }
    public function getEntity2()  { return $this->entity2;  }

    public function setId     ($value) { $this->onPropertySet('id',    $value); }
    public function setRole   ($value) { $this->onPropertySet('name',  $value); }
    public function setStatus ($value) { $this->onPropertySet('status',$value); }
    
    public function setName1  ($value) { $this->onPropertySet('name1',  $value); }
    public function setSort1  ($value) { $this->onPropertySet('sort1',  $value); }
    
    public function setName2  ($value) { $this->onPropertySet('name2',  $value); }
    public function setSort2  ($value) { $this->onPropertySet('sort2',  $value); }
    
    public function setEntity1($entity,$sort = null, $name = null) 
    { 
        $this->onPropertySet('entity1', $entity);
        if ($entity)
        {
            $this->onPropertySet('sort1', $sort);
            
            if ($name) $this->onPropertySet('name1', $name);
            else       $this->onPropertySet('name1', $entity->getName()); // Debug for now
        }
    }
    public function setEntity2($entity,$sort = null, $name = null) 
    { 
        $this->onPropertySet('entity2', $entity);
        if ($entity)
        {
            $this->onPropertySet('sort2', $sort);
            
            if ($name) $this->onPropertySet('name2', $name);
            else       $this->onPropertySet('name2', $entity->getName());   
        }
    }
}
?>
