<?php
namespace Cerad\Bundle\PersonBundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PersonRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad_person.repository';
    
    protected function getManager()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);

        return $manager;
    }
    public function testService()
    {
        $manager = $this->getManager();
        
        $this->assertEquals('Cerad\Bundle\PersonBundle\EntityRepository\PersonRepository', get_class($manager));
        
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\Person',      $manager->getClassName());
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\Person',      $manager->getPersonClassName());
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\PersonCert',  $manager->getPersonCertClassName());
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\PersonLeague',$manager->getPersonLeagueClassName());
        $this->assertEquals('Cerad\Bundle\PersonBundle\Entity\PersonPerson',$manager->getPersonPersonClassName());
    }
    public function testCreate()
    {
        $manager = $this->getManager();
        
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
                
        return array('personId' => $personId);
        
    }
    /**
     * @depends testCreate
     */
    public function testLoadPerson($data)
    {
        $personId = $data['personId'];
        
        // Verify depends works as expected
        $this->assertEquals(32, strlen($personId));

        $manager = $this->getManager();
        
        $this->assertEquals(32, strlen($personId));
         
        $person = $manager->find($personId);
        $this->assertEquals('Joe Blow', $person->getName());
        
        $personCert = $person->getCertRefereeAYSO();
        $this->assertEquals(13, strlen($personCert->getIdentifier()));

        $data['identifier'] = $personCert->getIdentifier();
        
        return $data;
    }
    /**
     * @depends testLoadPerson
     */
    public function testLoadPersonCerts($data)
    {
        $identifier = $data['identifier'];
        
        // Verify depends works as expected
        $this->assertEquals(13, strlen($identifier));

        $manager = $this->getManager();
        
        $certs = $manager->loadPersonCertsForIdentifier($identifier);
        
        $this->assertGreaterThan(0,count($certs));
        
        return $data;
    }
    /**
     * @depends testLoadPersonCerts
     */
    public function testDeletePerson($data)
    {
        // Verify depends works as expected
        $personId = $data['personId'];
        $this->assertEquals(32, strlen($personId));
        
        $manager = $this->getManager(); // $client->getContainer()->get($this->managerId);
        $person = $manager->find($personId);
        $manager->remove($person);
        $manager->flush();
        
        $person = $manager->find($personId);
        $this->assertEquals(null,$person);
        
        echo sprintf("\nPerson %s\n",$personId);
    }
}
?>
