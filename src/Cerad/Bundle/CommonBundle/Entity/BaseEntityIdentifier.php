<?php
namespace Cerad\Bundle\CommonBundle\Entity;

class BaseEntityIdentifier extends BaseEntity
{
    const SourceKey     = 'Key';      // This is the only one with special meaning?
    const SourceManual  = 'Manual';   // Made by hand
    const SourceArbiter = 'Arbiter';  // More of a source
    
    protected $id;
    protected $name;    // Debugging
    protected $value;   // Globally Unique
    protected $source;  // Might be handy
    protected $status = 'Active';  // Just Because
    
    protected $entity;
    
    public function getId    ()  { return $this->id;     }
    public function getName  ()  { return $this->name;   }
    public function getValue ()  { return $this->value;  }
    public function getStatus()  { return $this->status; }
    public function getSource()  { return $this->source; }
    public function getEntity()  { return $this->entity; }
    
    public function setId    ($value) { $this->onPropertySet('id',      $value); }
    public function setName  ($value) { $this->onPropertySet('name',    $value); }
    public function setValue ($value) { $this->onPropertySet('value',   $value); }
    public function setStatus($value) { $this->onPropertySet('status',  $value); }
    public function setSource($value) { $this->onPropertySet('source',  $value); }
    public function setEntity($value) { $this->onPropertySet('entity',  $value); }
    
}
?>
