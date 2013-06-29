<?php
namespace Cerad\Bundle\ArbiterBundle\Schedule\Import;

class ImportScheduleSlotXML extends ImportScheduleBase
{
    protected function processGameTeam($team,$game,$gameReportStatus,$name,$score)
    {
        $team->setLevel($game->getLevel());
        $team->setName ($name);
        
        // TODO: Deal with report but no actual game i.e. no score
        if ($gameReportStatus != 'No Report')
        {
            // int type casting is important
            $team->setScore((int)$score);
        }
        //$game->addTeam($team);
        
        return $team;
    }
    // Need to track new games because get some duplicates?
    protected $newGames = array();
    protected $params;
    
    protected function processRow($row)
    {
        // The Project
        $domainSub = $row['Sport'];
        $project = $this->getProject($this->source,$this->sport,$this->season,$this->domain,$domainSub);
        
        // The Level
        $level = $this->getLevel($project,$row['Level']);
        
        // The field
        $name    = $row['Site'];
        $siteSub = $row['Subsite'];
        $venue   = null;
        
        if ($siteSub)
        {
            $venue = $name;
            $name .= ' '  . $siteSub; // Need more thought here
            
            // SS #Merrimack# $MM01S$
            // echo sprintf("SS #%s# $%s$\n",$name,$siteSub);
        }
        $field = $this->getField($project,$name);
        
        // Typecast is important because the property change stuff is type specific
        $num = (int)$row['GameID'];
                
        /* ==========================================================
         * Check to see if already have game
         */
        $gameManager = $this->gameManager;
        $game = $gameManager->loadGameForProjectNum($project,$num);
        if (!$game)
        {
            // Some games are exported twice
            if (isset($this->newGames[$num]))
            {
                return;
            }
            $this->newGames[$num] = true;
            
            $game = $gameManager->newGame();
            $game->setNum($num);
            
            $gameManager->createGameTeam($game,'Home');
            $gameManager->createGameTeam($game,'Away');
            
            $this->persist($game);
        }
        // Could do an array thing here
        $game->setProject($project);
        $game->setLevel  ($level);
        $game->setField  ($field);
        $game->setStatus ($row['Status']);
        
        // 2013-03-08T16:30:00
        $dtBeg = \DateTime::createFromFormat('Y-m-d*H:i:s',$row['From_Date']);
        $dtEnd = \DateTime::createFromFormat('Y-m-d*H:i:s',$row['To_Date']);
        
        $game->setDtBeg($dtBeg);
        $game->setDtEnd($dtEnd);
        
        $gameReportStatus = $row['Report_Status'];

        $physicalHomeTeam = $this->getTeam($project,$row['Home_Team'],$level);
        $physicalAwayTeam = $this->getTeam($project,$row['Away_Team'],$level);
        
        $this->processGameTeam(
                $game->getHomeTeam(),
                $game,$gameReportStatus,
                $row['Home_Team'],$row['Home_Score']);
        
        $this->processGameTeam(
                $game->getAwayTeam(),
                $game,$gameReportStatus,
                $row['Away_Team'],$row['Away_Score']);
        
        /* ==========================================================
         * The officials
         */
        $slots  = $row['Slots_Total'];
        $slotsx = $game->getPersonSlotCount();
        if ($slotsx > $slots)
        {
            /* ========================================
             * Does not happen very often
             * Need a $game->removePersonForSlot
             * Easier to just clear everything
             */
            for($slot = 1; $slot <= $slotsx; $slots++)
            {
                $person = $game->getPersonForSlot($slot);
                $gameManager->remove($person);
            }
            $game->resetPersons();
            
            // Don't think this does what I want, need to delete extra slots
            //echo sprintf("#Slots changed: %d %d %d\n",$num,$slots,$slotsx);
            //die();
        }
        for($slot = 1; $slot <= $slots; $slot++)
        {
            switch($slot)
            {
                case 1: $prefix = 'First_' ; break;
                case 2: $prefix = 'Second_'; break;
                case 3: $prefix = 'Third_' ; break;
                case 4: $prefix = 'Fourth_'; break;
                case 5: $prefix = 'Fifth_' ; break;
            }
            $role = $row[$prefix . 'Position'];
            $name = $row[$prefix . 'Official'];
            
            $person = $game->getPersonForSlot($slot);
            if (!$person)
            {
                $gameManager->createGamePerson($game,$role,$slot,$name);
            }
            else
            {
                $person->setRole($role);
                $person->setName($name);
                $person->setStatus('Pending');
            }
        }
        $this->flush();
        
        return;
    }
    /* ====================================================================
     * Import a file
     */
    public function importFile($params,$reader = null)
    {
        // Stash some parameters
        $this->params = $params;
        $this->sport  = $params['sport'];
        $this->source = $params['source'];
        $this->season = $params['season'];
        $this->domain = $params['domain'];
        
        if (!$reader) throw new \Exception('ImportScheduleSlots with no xml reader');
       
        $this->gameManager->getEventManager()->addEventSubscriber($this);
        
        $this->results = new ImportScheduleResults();
        
        $this->results->inputFileName  = $params['inputFileName'];
        $this->results->clientFileName = $params['clientFileName'];
        $this->results->totalGameCount = 0;
        
        if ($params['output'] == 'Post') $this->persistFlag = true;

        // Kind of screw but oh well
        while ($reader->read() && $reader->name !== 'Detail');
        
        while($reader->name == 'Detail')
        {
            $row = array();
            while($reader->moveToNextAttribute()) 
            { 
                $row[$reader->name] = $reader->value;
            }
            $this->results->totalGamesCount++;
            $this->processRow($row);
            
            // On to the next one
            $reader->next('Detail');
        }
        $reader->close();
        
        $this->flush(true);
        
        $this->gameManager->getEventManager()->removeEventSubscriber($this);
         
        return $this->results;
    }
    /* ===============================================================
     * The main map
     */
    /* =========================================================================
    [sport] => Soccer
    [season] => SP2013
    [group] => NASOA
    [defaultGameStatus] => Normal
    [inputFileName] => /home/impd04/datax/arbiter/SP2013/NasoaSlots20130124.xml
    [num] => 402
    [dateTimeBegin] => 2013-01-26T10:00:00
    [dateTimeEnd] => 2013-01-26T11:15:00
    [groupSub] => MSSL
    [level] => MS-B
    [site] => John Hunt 4
    [siteSub] =>
    [homeTeamName] => Huntsville Boys
    [homeTeamScore] => 0
    [awayTeamName] => Whitesburg Boys
    [awayTeamScore] => 0
    [status] => Normal
    [officialSlots] => 3
    [officialRole1] => Referee
    [officialRole2] => AR1
    [officialRole3] => AR2
    [officialRole4] => No Fourth Position
    [officialRole5] => No Fifth Position
    [officialName1] =>
    [officialName2] =>
    [officialName3] =>
    [officialName4] => Empty
    [officialName5] => Empty
    [billTo] => MSSL, Mark Tillman
    [billAmount] => 0.00
    [billFees] => 0.00
    [gameNote] => No Note
    [gameNoteDate] =>
    [gameReportComments] =>
    [gameReportDateTime] => 1900-01-01T00:00:00
    [gameReportStatus] => No Report
    [gameReportOfficial] =>
     * 
     * Level
     * [domain] => NASOA
     * [sub]    => MSSL
     * [level]  => MS-B
     * [sport]  => Soccer
)    *
    */
    // map is not used, consider it documentation
    protected $mapx = array
    (
        'num'           => 'GameID',
        'dtBeg'         => 'From_Date',    // 2013-03-08T16:30:00
        'dtEnd'         => 'To_Date',
        'domainSub'     => 'Sport',        // AHSAA
        'level'         => 'Level',        // MS-B
        'venue'         => 'Site',
        'venueSub'      => 'Subsite',
        'homeTeamName'  => 'Home_Team',
        'homeTeamScore' => 'Home_Score',
        'awayTeamName'  => 'Away_Team',
        'awayTeamScore' => 'Away_Score',
        
        'status'        => 'Status',
        
        'officialSlots' => 'Slots_Total',
        
        'officialRole1' => 'First_Position',  // Referee 
        'officialRole2' => 'Second_Position', // AR1 (or possibly dual?
        'officialRole3' => 'Third_Position',  // AR2
        'officialRole4' => 'Fourth_Position', // 'No Fourth Position'
        'officialRole5' => 'Fifth_Position',  // 'No Fifth Position' 
        
        'officialName1' => 'First_Official', 
        'officialName2' => 'Second_Official', 
        'officialName3' => 'Third_Official', 
        'officialName4' => 'Fourth_Official',  // 'Empty'
        'officialName5' => 'Fifth_Official',   // 'Empty'
        
        'billTo'        => 'BillTo_Name',
        'billAmount'    => 'Bill_Amount',     // 100.00
        'billFees'      => 'Total_Game_Fees', //  37.00 ?
        
        'gameNote'      => 'Game_Note',    // 'No Note'
        'gameNoteDate'  => 'Note_Date=',   //  Blank
        
        'gameReportComments' => 'Game_Report_Comments',
        'gameReportDateTime' => 'Report_Posted_Date',   // 1900-01-01T00:00:00
        'gameReportStatus'   => 'Report_Status',        // 'No Report'
        'gameReportOfficial' => 'Reporting_Official',
        
    );

}
?>
