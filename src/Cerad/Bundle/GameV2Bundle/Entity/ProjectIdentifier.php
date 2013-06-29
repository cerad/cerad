<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityIdentifier as CommonBaseEntityIdentifier;

class ProjectIdentifier extends CommonBaseEntityIdentifier
{   
    public function getProject() { return $this->entity; }
    
    public function setProject($value) { $this->onPropertySet('entity', $value); }
}
?>
