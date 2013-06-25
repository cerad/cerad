<?php
namespace Cerad\Bundle\GameV2Bundle\EntityRepository;

class FieldRepository extends BaseRepository
{
    /* ------------------------------------
     * Your basic creator
     */
    public function createField($domain,$domainSub,$season,$name,$id = null)
    {
        $fieldClassName = $this->getClassName();
        $field          = new $fieldClassName();
        
        $field->setName     ($name);
        $field->setSeason   ($season);
        $field->setDomain   ($domain);
        $field->setDomainSub($domainSub);
        
        // Was ID Passed?
        if (!$id) $id = $this->hash(array($domain,$domainSub,$season,$name));
        $field->setId($id);
        
        return $field;
    }
    /* ------------------------------------
     * Loads existing level or optionally creates a new one
     */
    protected $cache;
    
    public function loadField($domain,$domainSub,$season,$name,$autoCreate = true)
    {
        /* ==============================================
         * A cache is required to avoid having to flush
         */
        $id = $this->hash(array($domain,$domainSub,$season,$name));
        if (isset($this->cache[$id])) return $this->cache[$id];
        
        $params = array
        (
            'name'      => $name,
            'season'    => $season,
            'domain'    => $domain,
            'domainSub' => $domainSub,
        );
        $field = $this->findOneBy($params);
        if ($field) 
        {
            $this->cache[$id] = $field;
            return $field;
        }
        if (!$autoCreate) return null;
        
        // Create a new one
        $field = $this->createField($domain,$domainSub,$season,$name,$id);
        $this->cache[$id] = $field;
        $this->persist($field);
        return $field;
    }
    /* -----------------------------------------------------
     * Load a set of field names
     */
    public function loadFieldChoices($params = array())
    {
        $qb = $this->createQueryBuilder('field');
        
        // Not sure how much to filter here
        // Fields are unique within an arbiter group
        $seasons    = $qb->getArrayForParam($params,'seasons');
        $domains    = $qb->getArrayForParam($params,'domains');    // Arbiter group
        $domainSubs = $qb->getArrayForParam($params,'domainSubs'); // Arbiter 'sport' aka sub-group
        
        // Build query
        $qb->addSelect('distinct field.name');
        
        $qb->andWhereEq('field.season',   $seasons);
        $qb->andWhereEq('field.domain',   $domains);
        $qb->andWhereEq('field.domainSub',$domainSubs);
        
        $qb->addOrderBy('field.name');
        
        $items = $qb->getQuery()->getArrayResult();
        
        $choices = array();
        foreach($items as $item)
        {
            $choices[$item['name']] = $item['name'];
        }
        return $choices;
    }
}
?>
