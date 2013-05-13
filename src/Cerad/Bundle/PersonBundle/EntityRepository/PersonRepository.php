<?php
namespace Cerad\Bundle\PersonBundle\EntityRepository;

use Doctrine\ORM\EntityRepository;

// PersonReg => PersonLeague?
class PersonRepository extends EntityRepository
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

    // Nice to make this configurable
    public function getPersonClassName      () { return $this->_entityName; }
    public function getPersonCertClassName  () { return $this->_entityName . 'Cert';   }
    public function getPersonLeagueClassName() { return $this->_entityName . 'League'; }
    public function getPersonPersonClassName() { return $this->_entityName . 'Person'; }
    
    public function newPerson()       { $className = $this->getPersonClassName      (); return new $className(); }
    public function newPersonCert()   { $className = $this->getPersonCertClassName  (); return new $className(); }
    public function newPersonLeague() { $className = $this->getPersonLeagueClassName(); return new $className(); }
    public function newPersonPerson() { $className = $this->getPersonPersonClassName(); return new $className(); }
    
    /* =============================================================
     * Load all certs for a given identifier
     */
    public function loadPersonCertsForIdentifier($identifier)
    {
        $repo = $this->_em->getRepository($this->getPersonCertClassName());
        return $repo->findAll(array('identifier' => $identifier));
    }
    /* =============================================================
     * Load all leagues for a given identifier
     */
    public function loadPersonLeaguesForIdentifier($identifier)
    {
        $repo = $this->_em->getRepository($this->getPersonLeagueClassName());
        return $repo->findAll(array('identifier' => $identifier));
    }
    /* =============================================================
     * Load all league for a given mem_id
     * 8 digit ayso vol id
     */
    public function loadPersonLeagueForMemId($id)
    {
        $repo = $this->_em->getRepository($this->getPersonLeagueClassName());
        
        $leagues = $repo->findBy(array('memId' => $id));
        
        // die('Find ' . $id . ' ' . count($leagues));
        
        if (count($leagues) == 1) return $leagues[0];
        
        return null;
    }
    /* ==============================================================
     * Clear out person tables for debugging
     */
    public function deletePersons()
    {
        $conn = $this->_em->getConnection();
        $conn->executeUpdate('DELETE FROM person_person;');
        $conn->executeUpdate('DELETE FROM person_league;');
        $conn->executeUpdate('DELETE FROM person_cert;');
        $conn->executeUpdate('DELETE FROM person;');
        
        $conn->executeUpdate('ALTER TABLE person_person AUTO_INCREMENT = 1;');
        $conn->executeUpdate('ALTER TABLE person_league AUTO_INCREMENT = 1;');
        $conn->executeUpdate('ALTER TABLE person_cert   AUTO_INCREMENT = 1;');
    }

}
?>
