<?php
namespace Cerad\Bundle\GameBundle\Tests\Manager;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectManagerTest extends WebTestCase
{
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.project.manager');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Manager\ProjectManager', get_class($manager));
    }
    public function testResetDatabase()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.project.manager');
        
        $manager->resetDatabase();
    }
    public function testRepo()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.project.manager');
        
        $repo = $manager->getRepository();
        
        $this->assertEquals('Doctrine\ORM\EntityRepository', get_class($repo));        
    }
    public function testProject()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.project.manager');
        
        $project = $manager->createProject('Soccer','SP2013','NASOA','MSSL');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Project', get_class($project));
        $this->assertEquals('SOCCERSP2013NASOAMSSL', $project->getHash());
        
        $manager->persist($project);
        $manager->flush  ($project);
    }
    public function testProjectLoad()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.project.manager');
        
        $project = $manager->loadForId('1');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Project', get_class($project));
        
        $project = $manager->loadForHash('SOCCERSP2013NASOAMSSL');
        
        $this->assertEquals('Cerad\Bundle\GameBundle\Entity\Project', get_class($project));
        
        $project = $manager->loadForHash('SOCCERSP2013NASOAMSSLxxx');
        
        $this->assertFalse(is_object($project));
        
        $this->assertEquals(false, $project);
     
    }
    public function testProjectQuery()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get('cerad.project.manager');
        
        $logger = new \Doctrine\DBAL\Logging\DebugStack();

        $manager
            ->getDatabaseConnection()
            ->getConfiguration()
            ->setSQLLogger($logger);  
        
        $hash = 'SOCCERSP2013NASOAMSSL';
        
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

        $manager = $client->getContainer()->get('cerad.project.manager');
        
        $project1 = $manager->processProject('Soccer','SP2013','NASOA','AHSAA');
        
        $hash1 = $project1->getHash();
        
        $project2  = $manager->loadForHash('SOCCERSP2013NASOAMSSL');
        $project1x = $manager->loadForHash($hash1);
       
        // Need a simple way to get the counts
        $items = $manager->getRepository()->findAll();
        $this->assertEquals(2,count($items));
        
    }
}

?>
