<?php
namespace Cerad\Bundle\PersonBundle\Entity;

/* =============================================
 * A person can be related to one or more teams
 * 
 * The team entity will usually be in the game bundle
 * It might alsmo make more sense to have a TeamPerson in the game bundle
 */
class PersonTeam
{
    protected $id;
    protected $person;
    protected $team;   // Should be an identifier and not a relation?
    protected $role;
    
    const RoleHeadCoach   = 'Head Coach';
    const RoleAsstCoach   = 'Asst Coach'; // Also run into Co Coaches
    const RoleManager     = 'Manager';
    
    const RoleParent = 'Parent';
    const RolePlayer = 'Player';
    
    /* ------------------------------------
     * Earning referee points for this team, 
     * probably does not belong here since
     * Madison need more information
     */
    const RolePoints = 'Points';
    
    protected $priority;  // Maybe for referee points?
    protected $points;    // For Madison, max points to earn for a given team
}
?>
