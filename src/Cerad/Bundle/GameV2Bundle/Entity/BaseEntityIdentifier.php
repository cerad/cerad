<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

class BaseEntityIdentifier extends BaseEntity
{
    const SourceKey     = 'Key';      // This is the only one with special meaning?
    const SourceManual  = 'Manual';   // Made by hand
    const SourceArbiter = 'Arbiter';  // More of a source
    
    protected $id;
    protected $value;   // Globally Unique
    protected $source;  // Might be handy
    protected $status = 'Active';  // Just Because
    
    public function getId()      { return $this->id;      }
    public function getValue()   { return $this->value;   }
    public function getStatus()  { return $this->status;  }
    public function getSource()  { return $this->source;  }
    
    public function setId     ($value) { $this->onPropertySet('id',      $value); }
    public function setValue  ($value) { $this->onPropertySet('value',   $value); }
    public function setSatus  ($value) { $this->onPropertySet('status',  $value); }
    public function setSource ($value) { $this->onPropertySet('source',  $value); }
    
}
?>
