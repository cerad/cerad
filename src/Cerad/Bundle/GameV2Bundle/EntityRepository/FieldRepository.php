<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

class FieldRepository extends BaseRepository
{
    public function getFieldClassName()           { return $this->_entityName; }
    public function getFieldIdentifierClassName() { return $this->_entityName . 'Identifier'; }
    
    public function newField()
    {
        $entityClassName = $this->getFieldClassName();
        return new $entityClassName();
    }
    public function newFieldIdentifier()
    {
        $entityClassName = $this->getFieldIdentifierClassName();
        return new $entityClassName();
    }
    public function findField($id)  { return $this->find($id); }
    public function findAllFields() { return $this->findAll(); }
    
    public function getFieldIdentifierManager() 
    {
        return $this->_em->getRepository($this->getFieldIdentifierClassName());
    }
    public function findFieldByIdentifierValue($value)
    {
        $fieldIdentifierManager = $this->getFieldIdentifierManager();
        
        $identifier = $fieldIdentifierManager->findOneByValue($value);
        
        return $identifier? $identifier->getField() : null;
    }
}
?>
