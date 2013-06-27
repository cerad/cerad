<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\EntityEntity;

use Cerad\Bundle\GameV2Bundle\Tests\EntityBaseTestCase;


class ProjectLevelTest extends EntityBaseTestCase
{    
    /** ========================================
     * Start with empty database
     */
    public function testResetDatabase()
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->masterManagerId);
        
        $manager->resetDatabase();
        
        return array();
    }
    /** ==========================================================
     * @depends testResetDatabase 
     * 
     * Create a new project with two identifiers
     */
    public function testNewProjects($info)
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->projectManagerId);
        
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
        $hash = $manager->hash(array('Soccer','SU2012','AYSONational','Games'));
        $identifier2 = $manager->newProjectIdentifier();
        $identifier2->setSource('Arbiter');
        $identifier2->setValue ($hash);
        $project->addIdentifier($identifier2);
         
        // Flush it
        $manager->persist($project);
        $manager->flush();
        $manager->clear();
        
        // Test it
        $project = $manager->findProjectByIdentifierValue($hash);
        $this->assertEquals('AYSO National Games 2012',$project->getName());
        
        // Pass on the data
        $info['projects']['project1']['id']   = $project->getId();
        $info['projects']['project1']['hash'] = $hash;
        
        // Just to start more or less clean
        $manager->clear();
        return $info;
    }
    /** ========================================================
     * @depends testNewProjects
     * 
     * Create new field with identifier
     */
    public function testNewLevels($info)
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->levelManagerId);
        
        // Create the fields
        $level1Name = 'U12B Core';
        $level2Name = 'U12B Rxtra';
        $level1 = $manager->newLevel(); $level1->setName($level1Name);
        $level2 = $manager->newLevel(); $level2->setName($level2Name);
        
        $manager->persist($level1);
        $manager->persist($level2);
        $manager->flush();
        
        // Arbiter Hashing Style
        $hash1 = $manager->hash(array('Soccer','SU2012',$level1Name));
        $identifier1 = $manager->newLevelIdentifier();
        $identifier1->setSource('Arbiter');
        $identifier1->setValue ($hash1);
        $level1->addIdentifier($identifier1);
        
        $hash2 = $manager->hash(array('Soccer','SU2012',$level2Name));
        $identifier2 = $manager->newLevelIdentifier();
        $identifier2->setSource('Arbiter');
        $identifier2->setValue ($hash2);
        $level2->addIdentifier($identifier2);
        
        $this->assertEquals(1,count($level1->getIdentifiers()));
         
        // Flush it
        $level1Id = $level1->getId();
        
        // The identifiers get saved just fine
        $manager->flush();
        $manager->clear();
   
        // Test Loading
        $level1 = $manager->findLevel($level1Id);
        $this->assertEquals($level1Name,$level1->getName());
        
        $level1 = $manager->findLevelByIdentifierValue($hash1);
        
        $this->assertEquals(true,is_object($level1));
        $this->assertEquals($level1Name,$level1->getName());
        
        // Pass on the data
        $info['levels']['level1']['id']   = $level1->getId();
        $info['levels']['level2']['id']   = $level2->getId();
        
        $info['levels']['level1']['name'] = $level1->getName();
        $info['levels']['level2']['name'] = $level2->getName();
        
        $info['levels']['level1']['hash'] = $hash1;
        $info['levels']['level2']['hash'] = $hash2;
        
        $manager->clear();
        return $info;
    }
    /** ===========================================
     * @depends testNewLevels
     */
    public function testNewProjectLevels($info)
    {
        $client = $this->createClientApp();

        // The managers
        $levelManager   = $client->getContainer()->get($this->levelManagerId);
        $projectManager = $client->getContainer()->get($this->projectManagerId);

        // Passed in info
        $level1Id   = $info['levels']['level1']['id'];
        $level2Id   = $info['levels']['level2']['id'];
        $project1Id = $info['projects']['project1']['id'];
        
        // Load stuff
        $level1   = $levelManager  ->findlevel($level1Id);
        $level2   = $levelManager  ->findlevel($level2Id);
        $project1 = $projectManager->findProject($project1Id);
        
        // Add the field, verify only added once
        $project1->addLevel($level1);
        $project1->addLevel($level2);
        $project1->addLevel($level1);

        $this->assertEquals(2,count($project1->getProjectLevels()));
         
        // Also verifies that the sysem knows the project entity was updated on relation change
        $projectManager ->flush(); $projectManager ->clear();
        $levelManager   ->flush(); $levelManager   ->clear();
        
        // Reload
        $level1   = $levelManager->findLevel($level1Id);
        $level2   = $levelManager->findLevel($level2Id);
        $project1 = $projectManager->findProject($project1Id);
        
        $this->assertEquals(2,count($project1->getProjectLevels()));
        
        $project1->addLevel($level1);
        
        $this->assertEquals(2,count($project1->getProjectLevels()));
        
        // Test access
        $projectLevels = $project1->getProjectLevels();
        
        $this->assertEquals($level1->getName(),$projectLevels[0]->getLevel()->getName());;
        $this->assertEquals($level2->getName(),$projectLevels[1]->getLevel()->getName());;
        
        // Done
        $projectManager ->flush(); $projectManager ->clear();
        $levelManager   ->flush(); $levelManager   ->clear();
        return $info;
    }
}

?>
