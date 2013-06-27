<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

class LevelRepository extends BaseRepository
{
    public function getLevelClassName()           { return $this->_entityName; }
    public function getLevelIdentifierClassName() { return $this->_entityName . 'Identifier'; }
    
    public function newLevel()
    {
        $entityClassName = $this->getLevelClassName();
        return new $entityClassName();
    }
    public function newLevelIdentifier()
    {
        $entityClassName = $this->getLevelIdentifierClassName();
        return new $entityClassName();
    }
    public function findLevel($id)  { return $this->find($id); }
    public function findAllLevels() { return $this->findAll(); }
   
    public function getLevelIdentifierManager() 
    {
        return $this->_em->getRepository($this->getLevelIdentifierClassName());
    }
    public function findLevelByIdentifierValue($value)
    {
        $levelIdentifierManager = $this->getLevelIdentifierManager();
        
        $identifier = $levelIdentifierManager->findOneByValue($value);
        
        return $identifier? $identifier->getLevel() : null;
    }
}
?>
