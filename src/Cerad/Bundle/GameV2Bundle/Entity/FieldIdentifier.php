<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

class FieldIdentifier extends BaseEntityIdentifier
{
    protected $field;
    
    public function getField() { return $this->field; }
    
    public function setField($value) { $this->onPropertySet('field', $value); }

}
?>
