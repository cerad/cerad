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
    public function getPersonPlanClassName  () { return $this->_entityName . 'Plan';   }
    public function getPersonLeagueClassName() { return $this->_entityName . 'League'; }
    public function getPersonPersonClassName() { return $this->_entityName . 'Person'; }
    
    public function newPerson()       { $className = $this->getPersonClassName      (); return new $className(); }
    public function newPersonCert()   { $className = $this->getPersonCertClassName  (); return new $className(); }
    public function newPersonPlan()   { $className = $this->getPersonPlanClassName  (); return new $className(); }
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
        $leagues = $repo->findBy(array('identifier' => $identifier));
        if (count($leagues) == 0) return null;
         
        // Some folks in eayso belong to more than one region
        // It's also possible that folks might have changed regions
        if (count($leagues) > 1) 
        {
            // Maybe send a message? Or calling process could check
        }
        return $leagues[0]->getPerson();
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
        $person       = null;
        
        // Have to have a league identifier
        if (isset($params['aysoVolunteerId']))
        {
            // AYSOV12345678
            $identifier = $params['aysoVolunteerId'];
            $personLeague = $this->loadPersonLeagueForIdentifier($identifier);
            
            // No updated is the person exists
            if ($personLeague) return $personLeague->getPerson();
            
            // Make one
            $person = $this->newPerson();
            $personLeague = $person->getVolunteerAYSO();
            $person->addLeague($personLeague);
                 
            $personLeague->setMemId (substr($identifier,5));
                
            $personLeague->setLeague($params['aysoRegionId']);
            
            // Always have primary person person
            $personPerson = $this->newPersonPerson();
            $personPerson->setRolePrimary();
            $personPerson->setMaster($person);
            $personPerson->setSlave ($person);
            $personPerson->setVerified('Yes');
            $person->addPerson($personPerson);
          
            // Referee badge
            if (isset($params['aysoRefereeBadge']))
            {
                $badge = $params['aysoRefereeBadge'];
                $cert  = $person->getCertRefereeAYSO();  // Creates one if needed
                $person->addCert($cert);
                
                $cert->setIdentifier($identifier);
                $cert->setBadgex($badge);
            }
        }
        // Check other possible leagues
        if (!$person) return null;
        
        // Update Person
        $person->setFirstName($params['personFirstName']);
        $person->setLastName ($params['personLastName']);
        $person->setNickName ($params['personNickName']);
        $person->setEmail    ($params['personEmail']);
        $person->setPhone    ($params['personPhone']);
      
        if ($params['personNickName']) $name = $params['personNickName']  . ' ' . $params['personLastName'];
        else                           $name = $params['personFirstName'] . ' ' . $params['personLastName'];
        
        $person->setName($name);
        
        return $person;
    }
    /* =================================================
     * Probably do not need, use person->getVolunteerAYSO
     */
    public function createPersonLeagueVolunteerAYSO()
    {
        $personLeagueClassName = $this->getPersonLeagueClassName();
        $personLeague = $personLeagueClassName::createVolunteerAYSO();
        return $personLeague;
    }
    /* =================================================
     * Create new project person
     */
    public function createPersonPlan($project,$person,$persist = false)
    {
        $personPlan = $this->newPersonPlan();
        
        if (is_object($project))
        {
            $personPlan->setProjectKey    ($project->getKey());
            $personPlan->setPlanProperties($project->getPlan());
        }
        else
        {
            $personPlan->setPlanProperties($project['plan']);
            $personPlan->setProjectKey    ($project['info']['key']);            
        }
        if ($person) $person->addPlan($personPlan);
        
        if ($persist) $this->persist($personPlan);
        
        return $personPlan;
    }
    /* ==============================================================
     * List of all the people for a given project
     */
    public function loadPersonsForProject($project)
    {
        // Grab the key
        $projectKey = is_object($project) ? $project->getKey() : $project;
        if (!$projectKey) return array();
        
        // Build the query, probably need to start at PersonPlan
        $qb = $this->createQueryBuilder('person');
        
        $qb->addSelect('plan, league, cert');
        
        $qb->leftJoin('person.plans',  'plan');
        $qb->leftJoin('person.certs',  'cert');
        $qb->leftJoin('person.leagues','league');
        
        $qb->andWhere($qb->expr()->eq('plan.projectKey',$qb->expr()->literal($projectKey)));
        
        $qb->orderBy('person.lastName,person.nickName,person.firstName');
        
        return $qb->getQuery()->getResult();      
        
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
    /* ==================================================
     * Simplify making person persons
     */
    public function createPersonPersonPrimary($master,$slave)
    {
        $personPerson = $this->newPersonPerson();
        $personPerson->setRolePrimary();
        $personPerson->setMaster($master);
        $personPerson->setSlave ($slave);
        $personPerson->setVerified('Yes');
        
        $master->addPerson($personPerson);
        
        return $personPerson;
    }
    /* ====================================================
     * Like the FOSUsermanager
     */
    public function updatePerson($person,$flush = false)
    {
        // Do we have a primary person person?
        $slave = $person->getPerson($person);
        if (!$slave)
        {
            $this->createPersonPersonPrimary($person,$person);
        }
        /* ===========================================================
         * Not sure if like this or not, don't really know which identifier to use
         
        // Transfer identifiers to cert if necessary, should always have a league
        $leagues = $person->getLeagues();
        $identifier = $leagues[0]->getIdentifier();
        
        $certs = $person->getCerts();
        foreach($certs as $cert)
        {
            if (!$cert->getIdentifier()) $cert->setidentifier($identifier);
        }
        */
        
        // Persist
        if (!$flush) return;
        $this->persist($person);
        $this->flush();
    }
    /* =============================================
     * TODO: This needs to lookup ayso/ussf id's and return the person
     */
    public function findByIdentifierValue($value)
    {
        return null;
    }
}
?>
