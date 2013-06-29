<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityEntity as CommonBaseEntityEntity;

class ProjectTeam extends CommonBaseEntityEntity
{
    public function getProject() { return $this->entity1; }
    public function getTeam()    { return $this->entity2; }
    
    public function setProject($entity) { return $this->setEntity1($entity); }
    public function setTeam   ($entity) { return $this->setEntity2($entity); }
}
?>
