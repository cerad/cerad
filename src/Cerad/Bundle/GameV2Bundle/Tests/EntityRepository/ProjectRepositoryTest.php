<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectRepositoryTest extends WebTestCase
{
    protected $managerId = 'cerad_gamev2.project.manager';
    
    public function testService()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\EntityRepository\ProjectRepository', get_class($manager));
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\Entity\Project',            $manager->getClassName());
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
        
        $project = $manager->createProject('Soccer','SP2013','NASOA','MSSL');
        
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\Entity\Project', get_class($project));
        $this->assertEquals('SOCCERNASOAMSSLSP2013',                   $project->getId());
        
        $manager->persist($project);
        $manager->flush  ($project);
        
        $projects = $manager->findAll();
        
        $this->assertEquals(1, count($projects));
    }
    public function testLoad()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $project = $manager->find('SOCCERNASOAMSSLSP2013');
        
        $this->assertTrue(is_object($project));
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\Entity\Project', get_class($project));
    }
    public function testQuery()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $logger = new \Doctrine\DBAL\Logging\DebugStack();

        $manager
            ->getDatabaseConnection()
            ->getConfiguration()
            ->setSQLLogger($logger);  
        
        $id = 'SOCCERNASOAMSSLSP2013';
        
        $project1 = $manager->find($id);
        $project2 = $manager->find($id);
        
        // Equals 1 only because of the manager's cache
        $this->assertEquals(1,count($logger->queries));
        
        /* var_sump($logger->gueries);
         * array(1) { [1]=> array(4) {
         * 
           ["sql"]=>
                     string(227) "SELECT t0.id AS id1, t0.season AS season2, t0.sport AS sport3, 
         *                        t0.domain AS domain4, t0.domain_sub AS domain_sub5, t0.title AS title6, t0.descx AS descx7, 
         *                        t0.status AS status8, t0.datax AS datax9 
         *                        FROM project t0 WHERE t0.id = ?"
         * 
           ["params"]=> array(1) { [0]=> string(21) "SOCCERNASOAMSSLSP2013"}
           ["types"] => array(1) { [0]=> string(6) "string" }
           ["executionMS"]=> float(0.00023198127746582)
        */
    }
    public function testLoadOrCreate()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
       
        $project1 = $manager->loadProject('Soccer','SP2013','NASOA','AHSAA',true);
        $this->assertEquals('SOCCERNASOAAHSAASP2013',$project1->getId());
        
        $project2 = $manager->loadProject('Soccer','SP2013','NASOA','MSSL',true);
        $this->assertEquals('SOCCERNASOAMSSLSP2013',$project2->getId());
        
    }
}

?>
