<?php
namespace Cerad\Bundle\GameV2Bundle\Tests\Entity;

use Cerad\Bundle\GameV2Bundle\Tests\EntityBaseTestCase;

/* ==============================================
 * Not a real test case
 * Basically empties it
 * Call it first in a project test suite
 */
class DatabaseResetTest extends EntityBaseTestCase
{    
    /** ========================================
     * Start with empty database
     */
    public function testResetDatabase()
    {
        $client = $this->createClientApp();

        $manager = $client->getContainer()->get($this->masterManagerId);
        
        $manager->resetDatabase();
        $manager->flush();
        $manager->clear();
        
        $this->assertEquals(0,count($manager->findAll()));
        return array();
    }
}

?>
