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
     * Load all league for a given identifier
     * Identifiers are unique
     */
    public function loadPersonLeagueForIdentifier($identifier)
    {
        $repo = $this->_em->getRepository($this->getPersonLeagueClassName());
        return $repo->findOneBy(array('identifier' => $identifier));
    }
    public function loadPersonForLeagueIdentifier($identifier)
    {
        $repo = $this->_em->getRepository($this->getPersonLeagueClassName());
        $league = $repo->findOneBy(array('identifier' => $identifier));
        if ($league) return $league->getPerson();
        return null;
    }
    /* =============================================================
     * Load all league for a given membership id
     * 8 digit ayso vol id
     * For now, these membership id's are unique
     */
    public function loadPersonLeagueForMemId($id)
    {
        $repo = $this->_em->getRepository($this->getPersonLeagueClassName());
        return $repo->findOneBy(array('memId' => $id));
    }
    public function loadPersonForLeagueMemId($id)
    {
        $repo = $this->_em->getRepository($this->getPersonLeagueClassName());
        $league = $repo->findOneBy(array('memId' => $id));
        if ($league) return $league->getPerson();
        return null;
    }
    /* ==============================================================
     * Eiether creates a new person or loads an existing one
     */
    public function createOrLoadPerson($params)
    {
        $personLeague = null;
        
        // Have to have a league identifier
        if (isset($params['aysoid']))
        {
            // AYSOV12345678
            $identifier = $params['aysoid'];
            $personLeague = loadPersonLeagueForIdentifier($identifier);
            if (!$personLeague)
            {
                // Make one
                $personLeague = $this->createPersonLeagueVolunteerAYSO();
                $personLeague->setLeague('AYSOR0894');
                $personLeague->setMemId ('12344321');
                
            }
        }
    }
    public function createPersonLeagueVolunteerAYSO()
    {
        $personLeagueClassName = $this->getPersonLeagueClassName();
        $personLeague = $personLeagueClassName::createVolunteerAYSO();
        return $personLeague;
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
