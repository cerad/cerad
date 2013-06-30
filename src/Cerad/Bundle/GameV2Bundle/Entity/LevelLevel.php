<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityEntity as CommonBaseEntityEntity;

class LevelLevel extends CommonBaseEntityEntity
{
    const RoleSame    = 'Same';
    
    public function getLevel1() { return $this->entity1; }
    public function getlevel2() { return $this->entity2; }
    
    public function setLevel1($entity) { return $this->setEntity1($entity); }
    public function setlevel2($entity) { return $this->setEntity2($entity); }

}
?>
