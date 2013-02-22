<?php
namespace Cerad\Bundle\GameBundle\Manager;

use Cerad\Bundle\GameBundle\Doctrine\QueryBuilder;

class BaseManager
{
    protected $em;
    protected $emName;         // Entity Manager Name (for form queries)
    protected $itemClassName; // Full root class name
    
    public function getEntityManager()      { return $this->em; }
    public function getEntityManagerName()  { return $this->emName; }
    public function getDatabaseConnection() { return $this->em->getConnection(); }
    
    public function __construct($em, $emName = 'default', $itemClassName = null)
    {
        $this->em            = $em;
        $this->emName        = $emName;
        $this->itemClassName = $itemClassName;
    }
    
    // Kind of hokay but think of this as an extended EntityManager
    public function clear()        { $this->em->clear(); }
    public function flush()        { $this->em->flush(); }
    public function remove ($item) { $this->em->remove ($item); }
    public function detach ($item) { $this->em->detach ($item); }
    public function persist($item) { $this->em->persist($item); }
    public function refresh($item) { $this->em->refresh($item); }
    
    // Return my own class with some convience methods
    public function createQueryBuilder($entityClassName = null, $alias = null)
    {
        $qb = new QueryBuilder($this->em);
        
        if ($entityClassName)
        {
            if ($alias) $qb->from($entityClassName,$alias);
            else        $qb->from($entityClassName);
        }
        return $qb;
    }
    /* ===================================================
     * Returns repository for root class
     * TODO: Run some benchmarks on large imports to see if using a repo helps or not
     * 
     * NOTE: The repos do not cache their results, each find results in a new query
     * The database itself will do a good job of querying so the performance impact is not clear
     * 
     * However, tracking new items not yet flushed would probably be done best with a cache
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->itemClassName);
    }
    public function loadForId($id)
    {
        return $this->getRepository()->find($id);
    }
    
    /* ===================================================
     * Probably going too far here
     */
    protected $hashCache;
    
    public function loadForHash($hash)
    {
        if (isset($this->hashCache[$hash])) return $this->hashCache[$hash];
        
        $item = $this->getRepository()->findOneBy(array('hash' => $hash));
        
        if ($item) $this->hashCache[$hash] = $item;
        
        return $item;
    }
    /* ===================================================
     * Empties all the game tables and resets their autoindex
     */
    public function truncate()
    {
        $conn = $this->getDatabaseConnection();
        $tables = array(
            'game_person',
            'game_team',
            'game',
            'field',
            'level',
            'project',
        );
        foreach($tables as $table)
        {
            $conn->executeUpdate('DELETE FROM ' . $table . ';');
            $conn->executeUpdate('ALTER TABLE ' . $table . ' AUTO_INCREMENT=1;');
        }
    }
    /* =====================================================
     * Reinitialize the entire database?
     * Works as expected except the user is not given permissions
     * Only root can see it?
     */
    public function resetDatabase()
    {
        $conn = $this->getDatabaseConnection();
        
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
}
?>
