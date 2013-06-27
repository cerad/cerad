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
    /* ===============================================
     * Creates a new field entity with one or more identifiers
     * Just too many variations
     */
    protected function createField($manager,$name,$hash1Values = null, $hash2Values = null)
    {
        $field = $manager->newField();
        $field->setName($name);
        
        // Arbiter Hashing Style
        $hash = $manager->hash(array('SU2012','Field #1'));
        $identifier2 = $manager->newFieldIdentifier();
        $identifier2->setSource('Arbiter');
        $identifier2->setValue ($hash);
        $field->addIdentifier($identifier2); 
        
        return $field;
    }
    protected function createFieldIdentifier($manager,$field,$hashValues)
    {
        // Arbiter Hashing Style
        $hash = $manager->hash($hashValues);
        $identifier2 = $manager->newFieldIdentifier();
        $identifier2->setSource('Arbiter');
        $identifier2->setValue ($hash);
        $field->addIdentifier($identifier2); 
        
        return $field;
    }
}
?>
