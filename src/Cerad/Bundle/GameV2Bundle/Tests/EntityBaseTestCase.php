<?php
namespace Cerad\Bundle\GameV2Bundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Component\Yaml\Yaml;

/* ====================================================
 * Probably getting too complicated but try to move
 * Some stuff here?
 */
class EntityBaseTestCase extends WebTestCase
{
    
    protected $gameManagerId    = 'cerad_gamev2.game.manager';
    protected $teamManagerId    = 'cerad_gamev2.team.manager';
    protected $fieldManagerId   = 'cerad_gamev2.field.manager';
    protected $levelManagerId   = 'cerad_gamev2.level.manager';
    protected $projectManagerId = 'cerad_gamev2.project.manager';
    
    protected $projectFactoryId = 'cerad_gamev2.project.factory';
    
    // Fake
    protected $masterManagerId  = 'cerad_gamev2.project.manager';

    protected function createClientApp()
    {
        $client = static::createClient();
        return $client;
    }
    protected static $fixtures;

    public static function setUpBeforeClass()
    {
        self::$fixtures = Yaml::parse(file_get_contents(__DIR__ . '/fixtures.yml'));
    }
    protected function findProject($client,$index = 0)
    {
        $manager = $client->getContainer()->get($this->projectManagerId);
       
        $fixtures = self::$fixtures['entities']['projects'];
        
        $id = $fixtures[$index]['id'];
        
        $project = $manager->find($id);
        
        $this->assertTrue(is_object($project));
        
        return $project;
    }
}
?>
