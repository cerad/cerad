<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

class LevelIdentifier extends BaseEntityIdentifier
{
    protected $level;
    
    public function getLevel() { return $this->level; }
    
    public function setLevel($value) { $this->onPropertySet('level', $value); }

}
?>
