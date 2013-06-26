<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

class ProjectIdentifier extends BaseEntity
{
    const SourceKey     = 'Key';      // This is the only one with special meaning?
    const SourceManual  = 'Manual';   // Made by hand
    const SourceArbiter = 'Arbiter';  // More of a source
    
    protected $id;
    protected $value;   // Globally Unique
    protected $source;  // Might be handy
    protected $project;
    
    public function getId()      { return $this->id;      }
    public function getValue()   { return $this->value;   }
    public function getSource()  { return $this->source;  }
    public function getProject() { return $this->project; }
    
    public function setId     ($value) { $this->onPropertySet('id',      $value); }
    public function setValue  ($value) { $this->onPropertySet('value',   $value); }
    public function setSource ($value) { $this->onPropertySet('source',  $value); }
    public function setProject($value) { $this->onPropertySet('project', $value); }
    
}
?>
