<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityIdentifier as CommonBaseEntityIdentifier;

class FieldIdentifier extends CommonBaseEntityIdentifier
{   
    public function getField() { return $this->entity; }
    
    public function setField($value) { $this->onPropertySet('entity', $value); }
}
?>
