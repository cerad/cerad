<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityEntity as CommonBaseEntityEntity;

class ProjectField extends CommonBaseEntityEntity
{
    public function getProject() { return $this->entity1; }
    public function getField()   { return $this->entity2; }
    
    public function setProject($entity) { return $this->setEntity1($entity); }
    public function setField  ($entity) { return $this->setEntity2($entity); }
}
?>
