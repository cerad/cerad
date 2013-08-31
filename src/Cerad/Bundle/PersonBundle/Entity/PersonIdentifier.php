<?php
namespace Cerad\Bundle\PersonBundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityIdentifier as CommonBaseEntityIdentifier;

class PersonIdentifier extends CommonBaseEntityIdentifier
{   
    public function getPerson() { return $this->entity; }
    
    public function setPerson($value) { $this->onPropertySet('entity', $value); }
}
?>
