<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityEntity as CommonBaseEntityEntity;

class FieldField extends CommonBaseEntityEntity
{
    const RoleSame    = 'Same';
    const RoleOverlap = 'Overlap';
    
    public function getField1() { return $this->entity1; }
    public function getField2() { return $this->entity2; }
    
    public function setField1($entity) { return $this->setEntity1($entity); }
    public function setField2($entity) { return $this->setEntity2($entity); }

}
?>
