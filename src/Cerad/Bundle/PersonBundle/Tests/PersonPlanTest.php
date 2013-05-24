<?php
namespace Cerad\Bundle\PersonBundle\Tests\EntityRepository;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Cerad\Bundle\PersonBundle\Entity\PersonPlan;

class PersonRepositoryTest extends WebTestCase
{
    public function testProject()
    {
        $client = static::createClient();
        
        // Global project key
        $projectKey = $client->getContainer()->getParameter('cerad_tourn_project_key');
        
        $this->assertEquals('AYSONationalGames2014', $projectKey);
        
        // Project info stored in project.yml
        $project = $client->getContainer()->getParameter('cerad_tourn_project');
        $projectInfo = $project['info'];
        $projectPlan = $project['plan'];
        
        $this->assertEquals($projectKey, $projectInfo['key']);
        
        $this->assertEquals('I will attend the games', $projectPlan['attending']['label']);
        
    }
    public function testEntity()
    {
        // Get the plan info
        $client = static::createClient();
        $project = $client->getContainer()->getParameter('cerad_tourn_project');
        
        $personPlan = new PersonPlan();
        $personPlan->setPlanProperties($project['plan']);
        
        $this->assertEquals('no', $personPlan->attending);
        
        $personPlan->attending = 'yes';
        $this->assertEquals('yes', $personPlan->attending);
       
        $this->assertTrue (isset($personPlan->refereeing));
        $this->assertFalse(isset($personPlan->refereeingx));  
    }
    public function testPersist()
    {
        $client = static::createClient();
        $project = $client->getContainer()->getParameter('cerad_tourn_project');
        
        $manager = $client->getContainer()->get('cerad_person.manager');
        $person = $manager->loadPersonForLeagueIdentifier('AYSOV99437977');

        /*
        $personPlan = new PersonPlan();
        $personPlan->setPlanProperties($project['plan']);
        $personPlan->setProjectKey($project['info']['key']);
        $person->addPlan($personPlan);
        $manager->persist($personPlan);
        */
        $personPlan = $manager->createPersonPlan($project,$person,true);
        
        $manager->flush();
    }
}
?>
