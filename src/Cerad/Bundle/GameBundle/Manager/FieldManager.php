<?php
namespace Cerad\Bundle\GameBundle\Manager;

use Cerad\Bundle\GameBundle\Entity\BaseEntity;

class FieldManager extends BaseManager
{
    public function createField($season,$domain,$domainSub,$name,$status = 'Active')
    {
        $itemClassName = $this->itemClassName;
        return $itemClassName::create($season,$domain,$domainSub,$name,$status);
    }
    public function newField()
    {
        return new $this->itemClassName();
    }
    public function processField($season,$domain,$domainSub,$name,$status = 'Active')
    {
        $hash = BaseEntity::hash(array($season,$domain,$domainSub,$name));
        
        // See if in database or cache
        $item = $this->loadForHash($hash);
        if ($item) return $item;
        
        // Create one
        $itemClassName = $this->itemClassName;
        $item = $itemClassName::create($season,$domain,$domainSub,$name,$status);
        
        $this->itemCache[$hash] = $item;
         
        // Getting really shakey here but remember that we expect the item to already exist most of the time
        $this->persist($item);
        $this->flush();
        
        return $item;
    }
}
?>
