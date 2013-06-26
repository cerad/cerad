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
    public $gameManager;
    
    public function __construct($projectManager,$levelManager,$fieldManager,$gameManager)
    {
        $this->projectManager = $projectManager;
        $this->levelManager   = $levelManager;
        $this->fieldManager   = $fieldManager;
        $this->gameManager    = $gameManager;
    }
}
?>
