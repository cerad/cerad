<?php
namespace Cerad\Bundle\GameBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Cerad\Bundle\GameBundle\Doctrine\QueryBuilder;

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

    /* ==========================================================
     * This also comes under the heading of hokay but
     * the convience methods in my query buider really reduces code
     */
    public function createQueryBuilder($alias)
    {
        $qb = new QueryBuilder($this->_em);
      //$qb->select($alias); // Allow distincts etc
        $qb->from($this->_entityName, $alias);
        return $qb;
    }
    
    /* ==========================================================
     * Need this if we decide to use the clear command on the entity manager
     */
    public function clearCache()
    {
        $this->hashCache = null;
    }
    public function createEntity($params = array())
    {
        $entityClassName = $this->_entityName;
        return $entityClassName::create($params);
    }
    public function newEntity()
    {
        return new $this->_entityName();
    }
    /* ==========================================
     * For lack of a better name
     * 1. Check cache
     * 2. Check database
     * 3. Create and add to cache if not found
     */
    public function processEntity($params = array(), $persist = false)
    {
        $entityClassName = $this->_entityName;
        
        $hash = $entityClassName::genHash($params);
        
        $entity = $this->loadForHash($hash);
        if ($entity) return $entity;
        
        /* ================================================
         * Probably should not do this but sometimes this is called without a sub domain
         * Because we are trying to find the sub domain
         * A create flag might be better?
         */
        if (!$persist) return null;
        
        // Add a new one
        $entity = $entityClassName::create($params);
        
        $this->hashCache[$hash] = $entity;
        
        // Getting really shakey here but remember that we expect the item to already exist most of the time
        if ($persist)
        {
            $this->persist($entity);
            $this->flush  ($entity); // Need this for now when creating new entities
        }
        return $entity;
    }
    public function getCount()
    {
        // Refine later
        return count($this->findAll());
    }
    /* ===================================================
     * Builting hash cache
     */
    protected $hashCache;
    
    public function loadForHash($hash)
    {
        if (isset($this->hashCache[$hash])) return $this->hashCache[$hash];
        
        $item = $this->findOneBy(array('hash' => $hash));
        
        if ($item) $this->hashCache[$hash] = $item;  // Even if null?
        
        return $item;
    }
    /* =====================================================
     * Reinitialize the entire database?
     * Works as expected except the user is not given permissions
     * Only root can see it?
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
}
?>
