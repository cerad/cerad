<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\Entity;

use Cerad\Bundle\GameV2Bundle\Tests\EntityBaseTestCase;

class TeamTest extends EntityBaseTestCase
{     
    /** ==========================================================
     * Creates some teams
     */
    public function testNewTeams()
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->teamManagerId);
        
        $valuex = null;
        
        $entityFixtures = isset(self::$fixtures['entities']['teams']) ? self::$fixtures['entities']['teams'] : array();
        foreach($entityFixtures as $entityFixture)
        {
            $id   = isset($entityFixture['id'  ]) ? $entityFixture['id'  ] : null;
            $role = isset($entityFixture['role']) ? $entityFixture['role'] : null;
            $name = isset($entityFixture['name']) ? $entityFixture['name'] : null;
                
            $entity = $manager->newEntity();
            
            if ($id) $entity->setId($id);
            
            $entity->setName($role);
            $entity->setName($name);
            
            $identifierFixtures = isset($entityFixture['identifiers']) ? $entityFixture['identifiers'] : array();
            
            foreach($identifierFixtures as $identifierFixture)
            {
                $value  = isset($identifierFixture['value' ]) ? $identifierFixture['value' ] : null;
                $source = isset($identifierFixture['source']) ? $identifierFixture['source'] : null;
                
                if (!$value)
                {
                    $value = $manager->hash(array($source,$entity->getName()));
                }
                $identifier = $manager->newIdentifier();
                
                $identifier->setValue ($value); $valuex = $value;
                $identifier->setSource($source);
                
                $entity->addIdentifier($identifier);
            }
            $manager->persist($entity);
        }
        $manager->flush();
        $manager->clear();
        
        // Quick identifier test
        $team = $manager->findByIdentifierValue($valuex);
        $this->assertTrue(is_object($team));
        $manager->clear();
         
        return;
        

    }
    /** ========================================================
     * @depends testNewTeams
     * 
     */
    public function testNewTeamTeams()
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->teamManagerId);
        
        $fixtures = isset(self::$fixtures['entities']['team_teams']) ? self::$fixtures['entities']['team_teams'] : array();
        foreach($fixtures as $fixture)
        {
            $id1  = isset($fixture['id1' ]) ? $fixture['id1' ] : null;
            $id2  = isset($fixture['id2' ]) ? $fixture['id2' ] : null;
            $role = isset($fixture['role']) ? $fixture['role'] : null;
            
            $team1 = $manager->find($id1);
            $team2 = $manager->find($id2);
                
            $team1->addTeam2($team2,$role);
            $team1->addTeam2($team2,$role);
            
            /*
            $entityEntity = $manager->newEntityEntity();
            $entityEntity->setRole($role);
           
            $entity1 = $manager->find($id1);
            $entity2 = $manager->find($id2);
            
            $entityEntity->setEntity1($entity1);
            $entityEntity->setEntity2($entity2);
            
            $entityEntity->setName1($entity1->getName());
            $entityEntity->setName2($entity2->getName());
            
            $manager->persist($entityEntity);*/
        }
        $manager->flush();
        $manager->clear();
        
        // See if truely linked
        $team0Id = self::$fixtures['entities']['teams'][0]['id'];
        $team3Id = self::$fixtures['entities']['teams'][3]['id'];
        $team0 = $manager->find($team0Id);
        
        $teamTeams2 = $team0->getTeamTeams2();
        
        $this->assertEquals(1,count($teamTeams2));
        $this->assertEquals($team3Id,$teamTeams2[0]->getEntity2()->getId());
        
        $manager->clear();
    }
    /** ========================================================
     * @depends testNewTeams
     * 
     */
    public function testNewProjectTeams()
    {
        $client = $this->createClientApp();

        $teamManager    = $client->getContainer()->get($this->teamManagerId);
        $projectManager = $client->getContainer()->get($this->projectManagerId);
        
        $teamFixtures    = self::$fixtures['entities']['teams'];
        $projectFixtures = self::$fixtures['entities']['projects'];
        
        $project = $projectManager->find($projectFixtures[0]['id']);
        
        $team0 = $teamManager->find($teamFixtures[0]['id']);
        $team1 = $teamManager->find($teamFixtures[1]['id']);
        
        $this->assertEquals(0,count($project->getProjectTeams()));
        $project->addTeam($team0);
        $project->addTeam($team1);
        $this->assertEquals(2,count($project->getProjectTeams()));
        
        $projectManager->flush();
        $projectManager->clear();
        
        $project = $projectManager->find($projectFixtures[0]['id']);
        $this->assertEquals(2,count($project->getProjectTeams()));

        $projectManager->clear();
        
    }
}

?>
