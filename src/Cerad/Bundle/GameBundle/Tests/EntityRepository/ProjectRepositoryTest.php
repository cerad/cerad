<?php
namespace Cerad\Bundle\GameBundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad.project.repository';
    
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\EntityRepository\ProjectRepository', get_class($manager));
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Project',            $manager->getClassName());
    }
    public function testResetDatabase()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $manager->resetDatabase();
    }
    public function testCreate()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $params = array('sport' => 'Soccer','season' => 'SP2013','domain' => 'NASOA','domainSub' => 'MSSL');
        
        $project = $manager->createEntity($params);
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Project', get_class($project));
        $this->assertEquals('SOCCERNASOAMSSLSP2013', $project->getHash());
        
        $manager->persist($project);
        $manager->flush  ($project);
        
        $this->assertEquals(1, $manager->getCount());
    }
    public function testProjectLoad()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $project = $manager->find('1');
        
        $this->assertTrue(is_object($project));
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Project', get_class($project));
        
        $project = $manager->loadForHash('SOCCERNASOAMSSLSP2013');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Project', get_class($project));
        
        $project = $manager->loadForHash('SOCCERSP2013NASOAMSSLxxx');
        
        $this->assertFalse(is_object($project));
        
        $this->assertEquals(false, $project);
     
    }
    public function testProjectQuery()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $logger = new \Doctrine\DBAL\Logging\DebugStack();

        $manager
            ->getDatabaseConnection()
            ->getConfiguration()
            ->setSQLLogger($logger);  
        
        $hash = 'SOCCERNASOAMSSLSP2013';
        
        $project1 = $manager->loadForHash($hash);
        $project2 = $manager->loadForHash($hash);
        
        // Equals 1 only because of the manager's cache
        $this->assertEquals(1,count($logger->queries));
        
        $this->assertEquals($hash,$project2->getHash());
       
        // This does not work without a request
        // $profile = $client->getProfile();
    }
    public function testProjectProcess()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $params = array('sport' => 'Soccer','season' => 'SP2013','domain' => 'NASOA','domainSub' => 'AHSAA');
       
        $project1 = $manager->processEntity($params,true);
        
        $hash1 = $project1->getHash();
        
        $project2  = $manager->loadForHash('SOCCERSP2013NASOAMSSL');
        $project1x = $manager->loadForHash($hash1);
       
        // Need a simple way to get the counts
        $this->assertEquals(2,$manager->getCount());
        
    }
}

?>
