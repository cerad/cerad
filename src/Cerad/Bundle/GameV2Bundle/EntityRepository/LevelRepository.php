<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

class LevelRepository extends BaseRepository
{
    /* ------------------------------------
     * Your basic creator
     */
    public function createLevel($sport,$domain,$domainSub,$name,$id = null)
    {
        $levelClassName = $this->getClassName();
        $level = new $levelClassName();
        $level->setName     ($name);
        $level->setSport    ($sport);
        $level->setDomain   ($domain);
        $level->setDomainSub($domainSub);
        
        // Was ID Passed?
        if (!$id) $id = $this->hash(array($sport,$domain,$domainSub,$name));
        $level->setId($id);
        
        return $level;
    }
    /* ------------------------------------
     * Loads existing level or optionally creates a new one
     */
    public function loadLevel($sport,$domain,$domainSub,$name,$autoCreate = true)
    {
        /* ==============================================
         * A cache is required to avoid having to flush
         */
        $id = $this->hash(array($sport,$domain,$domainSub,$name));
        if (isset($this->cache[$id])) return $this->cache[$id];
        
        $params = array
        (
            'name'      => $name,
            'sport'     => $sport,
            'domain'    => $domain,
            'domainSub' => $domainSub,
        );
        $level = $this->findOneBy($params);
        if ($level) 
        {
            $this->cache[$id] = $level;
            return $level;
        }
        if (!$autoCreate) return null;
        
        // Create a new one
        $level = $this->createLevel($sport,$domain,$domainSub,$name,$id);
        $this->cache[$id] = $level;
        $this->persist($level);
        return $level;
    }

    /* -----------------------------------------------------
     * TODO V2: Review this
     * Load a set of level choices
     */
    public function loadLevelChoices($params = array())
    {
        $qb = $this->createQueryBuilder('level');
        
        $sports     = $qb->getArrayForParam($params,'sports');
        $domains    = $qb->getArrayForParam($params,'domains');    // Arbiter group
        $domainSubs = $qb->getArrayForParam($params,'domainSubs'); // Arbiter 'sport' aka sub-group
        
        // Build query
        $qb->addSelect('distinct level.name');
        
        $qb->andWhereEq('level.sport',    $sports);
        $qb->andWhereEq('level.domain',   $domains);
        $qb->andWhereEq('level.domainSub',$domainSubs);
        
        $qb->addOrderBy('level.name');
        
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['name']] = $item['name'];
        }
        return $choices;
    }
    /* -----------------------------------------------------
     * Load a set of sub domains choices
     * Assume at leat one level for each sub domain of interest
     */
    public function loadDomainSubChoices($params = array())
    {
        $qb = $this->createQueryBuilder('level');
        
        $sports  = $qb->getArrayForParam($params,'sports');
        $domains = $qb->getArrayForParam($params,'domains');    // Arbiter group
        
        // Build query
        $qb->addSelect('distinct level.domainSub, level.domain');
        
        $qb->andWhereEq('level.sport',    $sports);
        $qb->andWhereEq('level.domain',   $domains);
        
        $qb->addOrderBy('level.domain, level.domainSub');
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            // Might have two sub groups in different groups with the same name
            // The group prefix would be misleading if multiple groups are selected
            $choices[$item['domainSub']] = $item['domain'] . ' ' . $item['domainSub'];
        }
        return $choices;       
     }
    /* -----------------------------------------------------
     * Load a set of domains choices
     */
    public function loadDomainChoices($params = array())
    {
        $qb = $this->createQueryBuilder('level');
        
        $sports = $qb->getArrayForParam($params,'sports');
        
        // Build query
        $qb->addSelect('distinct level.domain');
        
        $qb->andWhereEq('level.sport',    $sports);

        $qb->addOrderBy('level.domain');
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['domain']] = $item['domain'];
        }
        return $choices;       
     }
    /* -----------------------------------------------------
     * Load a set of sport choices
     */
    public function loadSportChoices($params = array())
    {
        $qb = $this->createQueryBuilder('level');
        
        // Build query
        $qb->addSelect('distinct level.sport');
        
        $qb->addOrderBy('level.sport');
       
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['sport']] = $item['sport'];
        }
        return $choices;       
     }
}
?>
