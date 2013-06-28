<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityIdentifier as CommonBaseEntityIdentifier;

class TeamIdentifier extends CommonBaseEntityIdentifier
{   
    public function getTeam() { return $this->entity; }
    
    public function setTeam($value) { $this->onPropertySet('entity', $value); }
}
?>
