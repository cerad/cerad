<?php
namespace Cerad\Bundle\GameBundle\Manager;

use Cerad\Bundle\GameBundle\Entity\BaseEntity;

class ProjectManager extends BaseManager
{
    /* ===================================================
     * Project Functions
     * These may turn out to be more trouble that it is worth
     * But it offers the chance that different project objects could be used
     */
    public function createProject($season,$sport,$domain,$domainSub,$status = 'Active')
    {
        $itemClassName = $this->itemClassName;
        return $itemClassName::create($season,$sport,$domain,$domainSub,$status);
    }
    public function newProject()
    {
        return new $this->itemClassName();
    }
    public function processProject($season,$sport,$domain,$domainSub,$status = 'Active')
    {
        // This is probably too fragile but use it for now
        // One alternative is to populate a Project entity but that seems ugly
        // Just keep in sync with the ProjectEntity
        $hash = BaseEntity::hash(array($season,$sport,$domain,$domainSub));
        
        // See if in database or cache
        $item = $this->loadForHash($hash);
        if ($item) return $item;
        
        // Create one
        $itemClassName = $this->itemClassName;
        $item = $itemClassName::create($season,$sport,$domain,$domainSub,$status);
        
        $this->itemCache[$hash] = $item;
         
        // Getting really shakey here but remember that we expect the item to already exist most of the time
        $this->persist($item);
        $this->flush();
        
        return $item;
    }
}
?>
