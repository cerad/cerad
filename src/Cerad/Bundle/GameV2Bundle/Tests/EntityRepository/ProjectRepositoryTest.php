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
        $this->assertEquals('Cerad\Bundle\GameV2Bundle\Entity\Project', $manager->getProjectClassName());
    }
    public function testResetDatabase()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $manager->resetDatabase();
    }
    public function testNew()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $project = $manager->newProject();
        $project->setName('AYSO National Games 2012');
        
        $project->setSource   ('Zayso');
        $project->setSport    ('Soccer');
        $project->setSeason   ('SU2012');
        $project->setDomain   ('AYSONational');
        $project->setDomainSub('Games');
         
        $identifier1 = $manager->newProjectIdentifier();
        $identifier1->setSource('Key');
        $identifier1->setValue ('AYSONatGames2012');
        $project->addIdentifier($identifier1);
        
        // Arbiter Hashing Style
        $value = $manager->hash(array('Soccer','SU2012','AYSONational','Games'));
        $identifier2 = $manager->newProjectIdentifier();
        $identifier2->setSource('Arbiter');
        $identifier2->setValue ($value);
        $project->addIdentifier($identifier2);
         
        $manager->persist($project);
        $manager->flush();
    }
    public function testLoadByIdentifier()
    {
        $client = static::createClient();

        $manager = $client->getContainer()->get($this->managerId);
        
        $project = $manager->findProjectByIdentifierValue('AYSONatGames2012');
        
        $this->assertEquals('AYSO National Games 2012',$project->getName());
        
        $project = $manager->findProjectByIdentifierValue('SOCCERSU2012AYSONATIONALGAMES');
        $this->assertEquals('AYSO National Games 2012',$project->getName());       
    }
}

?>
