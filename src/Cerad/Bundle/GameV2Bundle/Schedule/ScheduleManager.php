<?php
namespace Cerad\Bundle\GameV2Bundle\Schedule;

/* =============================================
 * This is basically a container to make injecting the assorted services easier
 * Not entirely sure it really should be used
 */
class ScheduleManager
{
    public $projectManager;
    public $levelManager;
    public $fieldManager;
    public $teamManager;
    public $gameManager;
    
    public function __construct($projectManager,$levelManager,$fieldManager,$teamManager,$gameManager)
    {
        $this->projectManager = $projectManager;
        $this->levelManager   = $levelManager;
        $this->fieldManager   = $fieldManager;
        $this->teamManager    = $teamManager;
        $this->gameManager    = $gameManager;
    }
}
?>
