<?php
namespace Cerad\Bundle\PersonBundle\Entity;

/* =================================================
 * One person (the master) can have some controll over their slaves
 * For example, the master can sign slaves up for games so they slaves will not need an account
 * The master should also be notified when changes will impact their slaves
 */
class PersonPerson extends BaseEntity
{
    protected $id;
    
    protected $master;
    protected $slave;
    
    const RoleFamily = 'Family'; // John Sloan and his 3 (4?) family referees
    const RolePeer   = 'Peer';   // Referee teams formed for tournaments
    
    protected $role;
    
    protected $project;  // NULL for families, non-null for tournament specific groupings
    
}
?>
