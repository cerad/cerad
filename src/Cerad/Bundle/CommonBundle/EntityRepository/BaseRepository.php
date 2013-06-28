<?php
namespace Cerad\Bundle\CommonBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Cerad\Bundle\CommonBundle\Doctrine\QueryBuilder;

class BaseRepository extends EntityRepository
{
    // Kind of hokay but think of this as an extended EntityManager
    public function clear()        { $this->_em->clear(); }
    public function flush()        { $this->_em->flush(); }
    public function merge  ($item) { $this->_em->merge  ($item); }
    public function remove ($item) { $this->_em->remove ($item); }
    public function detach ($item) { $this->_em->detach ($item); }
    public function persist($item) { $this->_em->persist($item); }
    public function refresh($item) { $this->_em->refresh($item); }
    
    public function getDatabaseConnection() { return $this->_em->getConnection(); }
    public function getEventManager      () { return $this->_em->getEventManager(); }
    
    /* ===================================================
     * Probably trying to do too much but many entities have
     * Identifiers, Project relations and self relations
     * Might be a good use for traits
     */
    public function getEntityClassName()           { return $this->_entityName; }
    public function getEntityIdentifierClassName() { return $this->_entityName . 'Identifier'; }
    public function getEntityProjectClassName()    { return $this->_entityName . 'Identifier'; }
    public function getEntityEntityClassName()     { return $this->_entityName . 'Identifier'; }
    
    public function newEntity()
    {
        $className = $this->getEntityClassName();
        return new $className();
    }
    public function newEntityIdentifier()
    {
        $className = $this->getEntityIdentifierClassName();
        return new $className();
    }
    /* ==========================================================
     * This also comes under the heading of hokay but
     * the convience methods in my query buider really reduces code
     */
    public function createQueryBuilder($alias)
    {
        $qb = new QueryBuilder($this->_em);
        $qb->from($this->_entityName, $alias);
        return $qb;
    }
    
    /* =====================================================
     * Reinitialize the entire database
     */
    public function resetDatabase()
    {
        $conn = $this->getEntityManager()->getConnection();
        
        $database = $conn->getDatabase();
        
        $schema = $conn->getSchemaManager()->createSchema();
        
        $sqls = $schema->toSql($conn->getDatabasePlatform());
        
        $conn->executeUpdate('DROP   DATABASE ' . $database . ';');
        $conn->executeUpdate('CREATE DATABASE ' . $database . ';');
        $conn->executeUpdate('USE '             . $database . ';');

        foreach($sqls as $sql)
        {
            $conn->executeUpdate($sql);
        }
    }
    /* ========================================================================
     * Standard hashing
     * Either value or array
     * Remove some punctuation
     * Return actual hash or canocial string
     */
    public function hash($value, $crc = false)
    {
        if (is_array($value))
        {
            $array = $value;
            $value = null;
            
            // Trim and cat
            array_walk($array, function($val) use (&$value) { $value .= trim($val); });
        }
        // Probably just get rid of everything but letters/numbers,undersocres
        $value = strtoupper(preg_replace("/[^a-zA-Z0-9_]/", '', $value));

        if (!$crc) return $value;
        
        return hash('crc32',$value);       
    }
}
?>
