<?php
namespace Cerad\Bundle\CommonBundle\Entity;

use Cerad\Bundle\CommonBundle\Functions\IfIsSet;

class BaseEntityIdentifier extends BaseEntity
{
    const SourceKey     = 'Key';      // This is the only one with special meaning?
    const SourceManual  = 'Manual';   // Made by hand
    const SourceArbiter = 'Arbiter';  // More of a source
    const SourceUSSFC   = 'USSFC';  // More of a source
    const SourceAYSOV   = 'AYSOV';  // More of a source
    const SourceAYSOP   = 'AYSOP';  // More of a source
    
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
    
    public function loadFromArray($fixture,$entity,$project)
    {       
        $value  = IfIsSet::exe($fixture,'value' );
        $source = IfIsSet::exe($fixture,'source');
        $status = IfIsSet::exe($fixture,'status');
                
        if (!$value) $value = $this->genIdentifierValue($entity,$project);
        
        $this->setName  ($entity->getName());
        $this->setValue ($value);
        $this->setSource($source);
                
        if ($status) $this->setStatus($status);
                
        $entity->addIdentifier($this);
        
        return $this;
    }    
}
?>
