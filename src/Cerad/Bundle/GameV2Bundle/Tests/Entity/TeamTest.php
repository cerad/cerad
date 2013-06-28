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
        
        $entityFixtures = isset(self::$fixtures['entities']['teams']) ? self::$fixtures['entities']['teams'] : array();
        foreach($entityFixtures as $entityFixture)
        {
            $id   = isset($entityFixture['id'  ]) ? $entityFixture['id'  ] : null;
            $name = isset($entityFixture['name']) ? $entityFixture['name'] : null;
                
            $entity = $manager->newEntity();
            
            if ($id) $entity->setId($id);
            
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
                $identifier = $manager->newEntityIdentifier();
                
                $identifier->setValue ($value);
                $identifier->setSource($source);
                
                $entity->addIdentifier($identifier);
            }
            $manager->persist($entity);
        }
        $manager->flush();
        
        return;
        
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
    }
    /** ========================================================
     * @depends testNewProjects
     * 
     * Create new field with identifier
     */
    public function sestNewFields($info)
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->fieldManagerId);
        
        // Create the fields
        $field1Name = 'Field #1';
        $field2Name = 'Field #2';
        $field1 = $manager->newField(); $field1->setName($field1Name);
        $field2 = $manager->newField(); $field2->setName($field2Name);
        
        // Arbiter Hashing Style
        $hash1 = $manager->hash(array('SU2012',$field1Name));
        $identifier1 = $manager->newFieldIdentifier();
        $identifier1->setSource('Arbiter');
        $identifier1->setValue ($hash1);
        $field1->addIdentifier($identifier1);
        
        $hash2 = $manager->hash(array('SU2012',$field2Name));
        $identifier2 = $manager->newFieldIdentifier();
        $identifier2->setSource('Arbiter');
        $identifier2->setValue ($hash2);
        $field2->addIdentifier($identifier2);
         
        // Flush it
        $field1Id = $field1->getId();
        $manager->persist($field1);
        $manager->persist($field2);
        $manager->flush();
        $manager->clear();
        
        // Test Loading
        $field1 = $manager->findField($field1Id);
        $this->assertEquals($field1Name,$field1->getName());
        
        $field1 = $manager->findFieldByIdentifierValue($hash1);
        $this->assertEquals($field1Name,$field1->getName());
        
        // Pass on the data
        $info['fields']['field1']['id']   = $field1->getId();
        $info['fields']['field2']['id']   = $field2->getId();
        
        $info['fields']['field1']['name'] = $field1->getName();
        $info['fields']['field2']['name'] = $field2->getName();
        
        $info['fields']['field1']['hash'] = $hash1;
        $info['fields']['field2']['hash'] = $hash2;
        
        $manager->clear();
        return $info;
    }
    /** ===========================================
     * @depends testNewFields
     */
    public function sestNewProjectFields($info)
    {
        $client = $this->createClientApp();

        // The managers
        $fieldManager   = $client->getContainer()->get($this->fieldManagerId);
        $projectManager = $client->getContainer()->get($this->projectManagerId);

        // Passed in info
        $field1Id   = $info['fields']['field1']['id'];
        $field2Id   = $info['fields']['field2']['id'];
        $project1Id = $info['projects']['project1']['id'];
        
        // Load stuff
        $field1   = $fieldManager  ->findField($field1Id);
        $field2   = $fieldManager  ->findField($field2Id);
        $project1 = $projectManager->findProject($project1Id);
        
        // Add the field, verify only added once
        $project1->addField($field1);
        $project1->addField($field2);
        $project1->addField($field1);

        $this->assertEquals(2,count($project1->getProjectFields()));
         
        // Also verifies that the sysem knows the project entity was updated on relation change
        $projectManager ->flush(); $projectManager ->clear();
        $fieldManager   ->flush(); $fieldManager   ->clear();
        
        // Reload
        $field1   = $fieldManager->findField($field1Id);
        $field2   = $fieldManager->findField($field2Id);
        $project1 = $projectManager->findProject($project1Id);
        
        $this->assertEquals(2,count($project1->getProjectFields()));
        
        $project1->addField($field1);
        
        $this->assertEquals(2,count($project1->getProjectFields()));
        
        // Test access
        $projectFields = $project1->getProjectFields();
        
        $this->assertEquals($field1->getName(),$projectFields[0]->getField()->getName());;
        $this->assertEquals($field2->getName(),$projectFields[1]->getField()->getName());;
        
        // Done
        $projectManager ->flush(); $projectManager ->clear();
        $fieldManager   ->flush(); $fieldManager   ->clear();
        return $info;
    }
    /** ===========================================
     * @depends testNewProjectFields
     */
    public function sestProjectFieldChoices($info)
    {
        $client = $this->createClientApp();

        // The Project
        $projectManager = $client->getContainer()->get($this->projectManagerId);
        $project1Id = $info['projects']['project1']['id'];
        $project1 = $projectManager->findProject($project1Id);

        $choices = $projectManager->findFieldChoices(array($project1->getId()));
        
        $this->assertEquals(2,count($choices));
    }
}

?>
