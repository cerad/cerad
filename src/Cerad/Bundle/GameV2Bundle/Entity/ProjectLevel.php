<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

class ProjectLevel extends BaseEntityEntity
{
    public function getProject() { return $this->entity1; }
    public function getLevel()   { return $this->entity2; }
    
    public function setProject($entity) { return $this->setEntity1($entity); }
    public function setLevel  ($entity) { return $this->setEntity2($entity); }
}
?>
