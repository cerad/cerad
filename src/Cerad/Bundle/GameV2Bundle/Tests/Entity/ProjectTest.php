<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\Entity;

use Cerad\Bundle\GameV2Bundle\Tests\EntityBaseTestCase;

class ProjectTest extends EntityBaseTestCase
{     
    /** ==========================================================
     * Creates some teams
     */
    public function testNewProjects()
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->projectManagerId);
        $factory = $client->getContainer()->get($this->projectFactoryId);
        
        $fixtures = isset(self::$fixtures['entities']['projects']) ? self::$fixtures['entities']['projects'] : array();
        foreach($fixtures as $fixture)
        {
            $factory->createFromFixture($fixture,true);
        }
        $manager->flush();
    }
}
?>
