<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityEntity as CommonBaseEntityEntity;

class TeamTeam extends CommonBaseEntityEntity
{
    const RoleSame = 'Same';
    
    public function getTeam1() { return $this->entity1; }
    public function getTeam2() { return $this->entity2; }
    
    public function setTeam1($entity) { return $this->setEntity1($entity); }
    public function setTeam2($entity) { return $this->setEntity2($entity); }

}
?>
