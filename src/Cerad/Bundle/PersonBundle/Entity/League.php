<?php
namespace Cerad\Bundle\PersonBundle\Entity;

/* ==============================================
 * This is a fake non-persistable entity to allow linking
 * PersonLeague to a league even if the GameBundle is not available
 * Eventually it should implement the Model/LeagueInterface
 */
class League
{
    public function getDesc2() { return null; }
}
?>
