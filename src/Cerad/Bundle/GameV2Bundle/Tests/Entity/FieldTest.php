<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\Entity;

use Cerad\Bundle\GameV2Bundle\Tests\EntityBaseTestCase;

use Cerad\Bundle\CommonBundle\Functions\IfIsSet;

class FieldTest extends EntityBaseTestCase
{     
    protected function findProject($client,$index = 0)
    {
        $manager = $client->getContainer()->get($this->projectManagerId);
       
        $fixtures = self::$fixtures['entities']['projects'];
        
        $id = $fixtures[$index]['id'];
        
        $project = $manager->find($id);
        
        $this->assertTrue(is_object($project));
        
        return $project;
    }
    /** ==========================================================
     * Creates some teams
     */
    public function testNewFieldss()
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->fieldManagerId);

        $project = $this->findProject($client);
        
        $fixtures = IfIsSet::exe(self::$fixtures['entities'],'fields',array());
        
        foreach($fixtures as $fixture)
        {
            // On disadvantage is needint to new here
            $field = $manager->newEntity();
            $field->loadFromArray($fixture,$project);
            
            $manager->persist($field);
        }
        $manager->flush();
        $manager->clear();
        
    }
}
?>
