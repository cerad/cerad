<?php
namespace Cerad\Bundle\ArbiterBundle\Schedule\Import;

class ImportScheduleResults
{
    public $totalGamesCount    = 0;
    public $totalGamesInserted = 0;
    public $totalGamesUpdated  = 0;
    
    public $totalGameTeamsUpdated   = 0;
    public $totalGamePersonsUpdated = 0;
    
    public $inputFileName;
    public $clientFileName;
    
    public $duration;
    public $memory;
    
    public function __toString()
    {
        ob_start();
        
        echo sprintf("File %s, Games Tot: %4d, Ins: %4d, Upd: %4d, GTU: %4d, GPU: %4d, DUR: %5d, MEM: %d\n",
                $this->clientFileName,
                $this->totalGamesCount,
                $this->totalGamesInserted,
                $this->totalGamesUpdated,
                $this->totalGameTeamsUpdated,
                $this->totalGamePersonsUpdated,
                $this->duration,
                $this->memory
        );
        
        return ob_get_clean();
    }
}
?>
