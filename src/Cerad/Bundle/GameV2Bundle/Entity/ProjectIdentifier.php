<?php
namespace Cerad\Bundle\GameV2Bundle\Entity;

class ProjectIdentifier extends BaseEntityIdentifier
{
    protected $project;
    
    public function getProject() { return $this->project; }
    
    public function setProject($value) { $this->onPropertySet('project', $value); }

}
?>
