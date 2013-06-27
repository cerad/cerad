<?php
namespace Cerad\Bundle\GameV2Bundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/* ====================================================
 * Probably getting too complicated but try to move
 * Some stuff here?
 */
class EntityBaseTestCase extends WebTestCase
{
    /* ==============================================
     * Put the ids here to make it easy to access
     * Note that master is a fake for now
     * All the managers point to the same database
     */
    protected $masterManagerId  = 'cerad_gamev2.project.manager';
    
    protected $projectManagerId = 'cerad_gamev2.project.manager';
    protected $fieldManagerId   = 'cerad_gamev2.field.manager';
    protected $levelManagerId   = 'cerad_gamev2.level.manager';
    protected $levelGameId      = 'cerad_gamev2.game.manager';

    protected function createClientApp()
    {
        $client = static::createClient();
        return $client;
    }
}
?>
