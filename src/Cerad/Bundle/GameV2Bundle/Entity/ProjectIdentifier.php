<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

use Cerad\Bundle\CommonBundle\Entity\BaseEntityIdentifier as CommonBaseEntityIdentifier;

class ProjectIdentifier extends CommonBaseEntityIdentifier
{   
    public function getProject() { return $this->entity; }
    
    public function setProject($value) { $this->onPropertySet('entity', $value); }
    
    // Not yet tested
    public function genIdentifierValue($entity,$project)
    {   
        $values = array();
        
        $values[] = $project->getSource();
        
        if ($entity->getName()) $values[] = $entity->getName();
        
        else
        {
            $values[] = $project->getSport();
            $values[] = $project->getSeason();
            $values[] = $project->getDomain();
            $values[] = $project->getDomainSub();
        }
        return $this->hash($values);        
    }
}
?>
