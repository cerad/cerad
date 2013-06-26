<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

class ProjectField extends BaseEntityProjectRelation
{
    public function getField() { return $this->entity; }
    
    public function setField($entity) { return $this->setEntity($entity); }
}
?>
