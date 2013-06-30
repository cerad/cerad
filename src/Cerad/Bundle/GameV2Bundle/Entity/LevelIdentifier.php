<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityIdentifier as CommonBaseEntityIdentifier;

class LevelIdentifier extends CommonBaseEntityIdentifier
{   
    public function getLevel() { return $this->entity; }
    
    public function setLevel($value) { $this->onPropertySet('entity', $value); }
}
?>
