<?php
namespace Cerad\Bundle\ArbiterBundle\Schedule\Import;

use Symfony\Component\Stopwatch\Stopwatch;

class ImportScheduleMaster
{
    protected $manager;
    protected $excel;
    
    public function __construct($manager,$excel = null)
    {
        $this->manager = $manager;
        $this->excel   = $excel;
    }
    public function importFileXML($params)
    {
        $inputFileName = $params['inputFileName'];
        
        // Must be a report file
        $reader = new \XMLReader();
        $reader->open($inputFileName,null,LIBXML_COMPACT | LIBXML_NOWARNING);

        // Position to Report node
        if (!$reader->next('Report')) 
        {
            $reader->close();
            return null;
        }
        // Verify report type
       $reportType = $reader->getAttribute('Name');
       switch($reportType)
       {
           case 'Games with Slots': $importClass = 'Cerad\Bundle\ArbiterBundle\Schedule\Import\ImportScheduleSlotXML'; break;
           default:
               $reader->close();
               return null;
       }
       $import = new $importClass($this->manager);
       
       return $import->importFile($params,$reader);  
    }
    public function importFileXLS($params)
    {
        $inputFileName = $params['inputFileName'];
        
        $reader = $this->excel->load($inputFileName);

        $ws = $reader->getSheet(0);

        $rows = $ws->toArray();
     
        $importClass = 'Cerad\Bundle\ArbiterBundle\Schedule\Import\ImportSchedulePortXLS';
        
        $import = new $importClass($this->manager);
        
        return $import->importFile($params,$rows);
    }        
    public function importFileTXT($params)
    {
        $inputFileName = $params['inputFileName'];
        
        $fp = fopen($inputFileName,'rt');

        $importClass = 'Cerad\Bundle\ArbiterBundle\Schedule\Import\ImportSchedulePortTXT';
        
        $import = new $importClass($this->manager);
        
        return $import->importFile($params,$fp);
    }
    public function importFileCSV($params)
    {
        $inputFileName = $params['inputFileName'];
        
        $fp = fopen($inputFileName,'rt');

        $importClass = 'Cerad\Bundle\ArbiterBundle\Schedule\Import\ImportScheduleSlotCSV';
        
        $import = new $importClass($this->manager);
        
        return $import->importFile($params,$fp);
    }
    public function importFile($params)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('importFile');
        
        // Do the truncate
        if (isset($params['truncate']) && $params['truncate']) 
        {
            // Probably should just truncate
            $this->manager->gameManager->resetDatabase();
        }
        // Check file type
        if (isset($params['clientFileName'])) $ext = pathinfo($params['clientFileName'], PATHINFO_EXTENSION);
        else
        {
            $params['clientFileName'] = pathinfo  ($params['inputFileName'],  PATHINFO_BASENAME);
            
            $ext = pathinfo($params['inputFileName'],  PATHINFO_EXTENSION);
        }
        // XML Slots
        switch($ext)
        {
            case 'xml': $results = $this->importFileXML($params); break;
            case 'xls': $results = $this->importFileXLS($params); break;
            case 'txt': $results = $this->importFileTXT($params); break;
            case 'csv': $results = $this->importFileCSV($params); break;
            default:
                throw new \Exception('Unsupported file type');
        }
        $event = $stopwatch->stop('importFile');
        $results->duration = $event->getDuration();
        $results->memory   = $event->getMemory();
        
        return $results;  
    }
}
?>
