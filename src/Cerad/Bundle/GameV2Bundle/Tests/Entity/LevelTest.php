<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\Entity;

use Cerad\Bundle\GameV2Bundle\Tests\EntityBaseTestCase;

use Cerad\Bundle\CommonBundle\Functions\IfIsSet;

class LevelTest extends EntityBaseTestCase
{     
    /** ==========================================================
     * Creates some levels
     */
    public function testNewLevels()
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->levelManagerId);

        $project = $this->findProject($client);
        
        $fixtures = IfIsSet::exe(self::$fixtures['entities'],'levels',array());
        
        foreach($fixtures as $fixture)
        {
            // On disadvantage is needint to new here
            $level = $manager->newEntity();
            $level->loadFromArray($fixture,$project);
            
            $manager->persist($level);
        }
        $manager->flush();
        $manager->clear();
        
        // Test a load by
    }
}
?>
