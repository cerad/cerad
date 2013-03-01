<?php
namespace Cerad\Bundle\PersonBundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PersonRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad.person.repository';
    
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals('Cerad\Bundle\PersonBundle\EntityRepository\PersonRepository', get_class($manager));
        
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\Person',      $manager->getClassName());
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\Person',      $manager->getPersonClassName());
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\PersonCert',  $manager->getPersonCertClassName());
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\PersonLeague',$manager->getPersonLeagueClassName());
    }
    public function testCreate()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $person = $manager->newPerson();
        $person->setName('Joe Blow');
        $person->setEmail('joe@gmail.com');
        $personId = $person->getId();
        
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\Person', get_class($person));
        $this->assertEquals('Joe Blow', $person->getName());
        $this->assertEquals(32, strlen($personId));
        
        $personLeagueClassName = $manager->getPersonLeagueClassName();
        $personLeague = $personLeagueClassName::createVolunteerAYSO();
        $personLeague->setLeague('AYSOR0894');
        $personLeague->setMemId ('12344321');
        $person->addLeague($personLeague);
        
        // Identifier is set by setMemId
        $identifier = $personLeague->getIdentifier();
         
        $personCert = $manager->newPersonCert();
        $personCert->setFed('AYSO');
        $personCert->setRole('Referee');
        $personCert->setIdentifier($identifier);
        
        $person->addCert($personCert);
        
        $manager->persist($person);
        $manager->flush();
                
        return $personId;
        
    }
    /**
     * @depends testCreate
     */
    public function testLoadPerson($personId)
    {
        // Verify depends works as expected
        $this->assertEquals(32, strlen($personId));
        
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals(32, strlen($personId));
         
        $person = $manager->find($personId);
        $this->assertEquals('Joe Blow', $person->getName());
        
        $personCert = $person->getCertRefereeAYSO();
        $this->assertEquals(13, strlen($personCert->getIdentifier()));

        return $personCert->getIdentifier();
    }
    /**
     * @depends testLoadPerson
     */
    public function testLoadPersonCerts($identifier)
    {
        // Verify depends works as expected
        $this->assertEquals(13, strlen($identifier));
        
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $certs = $manager->loadPersonCertsForIdentifier($identifier);
        
        $this->assertGreaterThan(0,count($certs));
    }
}
?>
