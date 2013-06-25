<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

class BaseRepository extends EntityRepository
{
    // Kind of hokay but think of this as an extended EntityManager
    public function clear()        { $this->_em->clear(); }
    public function flush()        { $this->_em->flush(); }
    public function remove ($item) { $this->_em->remove ($item); }
    public function detach ($item) { $this->_em->detach ($item); }
    public function persist($item) { $this->_em->persist($item); }
    public function refresh($item) { $this->_em->refresh($item); }
    
    public function getDatabaseConnection() { return $this->_em->getConnection(); }
    public function getEventManager      () { return $this->_em->getEventManager(); }

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
