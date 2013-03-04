<?php
namespace Cerad\Bundle\ArbiterBundle\Schedule\Import;

/* =====================================================================
 * Reading a text/csv file is very fast
 * Fragile because the commas are not escaped
 * 
 * Initial tests in which games were merely scanned and counted indicated that
 * it takes longer to scan the csv file than it does to scan the xml file
 * Both process use about the ame abount of memory
 * 
 * Somwhat unexpected result.  For now, just going with the xml reader.
 * 
 * So this scanner is not finished.
 * 
 */
class ImportScheduleSlotCSV extends ImportScheduleBase
{
    /* =======================================================================
     * Process one row from the stream
     */
    protected $game;
    protected $newGames = array();
    
    /* =======================================================================
     * Process one row from the stream
     * Most of time get 33 columns
     * 
     * Sometimes however only get 17 because the report breaks in half
     * So then the next one is basically an append though in some cases
     * appear to get an empty row as well
     */
    public function processRowDebug($row)
    {
        $count = count($row);
        if (!$count || !$row[0]) return;

        if ($count == 33) 
        {
            if ($row[1] == 'Game_LinkID') return;
            $this->importGame($row);
            return;
            
            print_r($row); die();
        }
        if ($count == 17) 
        {
            //print_r($row);
            //$this->printNext = true;
            return;
        }
        if ($count == 37) // Some stuff tavked on at the end? 20 = slot count
        {
            // print_r($row);
            //$this->printNext = true;
            return;
        }
        //echo sprintf("%d %s\n",$count,$row[0]);
        return;
    }
    /* =======================================================================
     * Main entry point
     */
    public function importFile($params,$fp)
    {
        if (!$fp) throw new \Exception('ImportScheduleSlotCSV with no file pointer');
        
        $this->params = $params;
        $this->game   = null;
        
        $this->gameManager->getEventManager()->addEventSubscriber($this);
        
        $this->results = new ImportScheduleResults();
        
        $this->results->inputFileName  = $params['inputFileName'];
        $this->results->clientFileName = $params['clientFileName'];
        $this->results->totalGameCount = 0;
        
        if ($params['output'] == 'Post') $this->persistFlag = true;
        
        while($row = fgetcsv($fp,4095,','))
        {
            // Get rid of all the trim nonsense
            // array_walk($row, create_function('&$val', '$val = trim($val);'));
            
            $this->processRowDebug($row);
        }
        if ($this->game) $this->importGame($this->game);
        
        fclose($fp);
        
        $this->flush(true);
        
        $this->gameManager->getEventManager()->removeEventSubscriber($this);
         
        return $this->results;
    }
    /* =======================================================================
     * If all the extract majic worked then have a game array
     */
    public function importGame($row)
    {
        $gameManager = $this->gameManager;
        
        $this->results->totalGamesCount++;

        // Trying to avoid creating multiple arrays for each row
        $params = array(
            'season'    => $this->params['season'],
            'sport'     => $this->params['sport'], 
            'domain'    => $this->params['domain'],
            'domainSub' => $row         [4],
        );
        // Some basic info      
        $project = $this->projectManager->processEntity($params,$this->persistFlag);
     
        $params['name'] = $row[5];
        $level = $this->levelManager->processEntity($params,$this->persistFlag);
        
        $params['venue']    = $row[6];
        $params['venueSub'] = $row[7];
        unset($params['name']);
        
        $field = $this->fieldManager->processEntity($params, $this->persistFlag);
     
        // Typecast is important because the property change stuff is type specific
        $num = (int)$row[0];
        
        // Now can get the existing game
        $game = $gameManager->loadGameForProjectNum($project,$num);
        if (!$game)
        {
            if (isset($this->newGames[$num]))
            {
                return;
            }
            $this->newGames[$num] = true;
            
            $game = $gameManager->createGame();
            $this->persist($game);
        }
        // Could do an array thing here
        $game->setNum    ($num);
        $game->setProject($project);
        $game->setLevel  ($level);
        $game->setField  ($field);
        $game->setStatus ($row[15]);
        
        // 1/26/2013 10:00:00 AM
        $dtBeg = \DateTime::createFromFormat('m/d/Y*h:i:s A',$row[2]);
        $dtEnd = \DateTime::createFromFormat('m/d/Y*h:i:s A',$row[3]);
        
        $game->setDtBeg($dtBeg);
        $game->setDtEnd($dtEnd);
        
        $gameReportStatus = $row[18];
        
        $this->processGameTeam(
                $gameManager->createGameTeamHome(),
                $game,$gameReportStatus,
                $row[8],$row[9]);
        
        $this->processGameTeam(
                $gameManager->createGameTeamAway(),
                $game,$gameReportStatus,
                $row[10],$row[11]);

        /* ==========================================================
         * The officials
         */
        $slots  = $row[20];
        $slotsx = $game->getPersonSlotCount();
        if (!$slotsx && ($slotsx != $slots))
        {
            // Changed crew, just start over
            $game->resetPersons();
        }
        for($slot = 1; $slot <= $slots; $slot++)
        {
            switch($slot)
            {
                case 1: $index = 21 ; break;
                case 2: $index = 23; break;
                case 3: $index = 25 ; break;
                case 4: $index = 27; break;
                case 5: $index = 29 ; break;
            }
            $role = $row[$index];
            $name = $row[$index+1];
            
            $person = $game->getPersonForSlot($slot);
            if (!$person)
            {
                $person = $this->gameManager->createGamePerson(array('slot' => $slot, 'role' => $role, 'name' => $name));
                $game->addPerson($person);
            }
            else
            {
                $person->setRole($role);
                $person->setName($name);
            }
        }
        
        // Flush any changes
        $this->flush();
    }
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
        $game->addTeam($team);
        
        return $team;
    }
    /* ----------------------------------------------------------------
     *     
    [ 0] => 402
    [ 1] =>
    [ 2] => 1/26/2013 10:00:00 AM
    [ 3] => 1/26/2013 11:15:00 AM
    [ 4] => MSSL
    [ 5] => MS-B
    [ 6] => John Hunt 4
    [ 7] =>
    [ 8] => Huntsville Boys
    [ 9] => 0
    [10] => Whitesburg Boys
    [11] => 0
    [12] => MSSL, Mark Tillman
    [13] => 0.00
    [14] => 0.00
    [15] => Normal
    [16] => assumed played
    [17] => 2/2/2013 8:15:00 PM
    [18] => Completed
    [19] => Hensley, Kim
    [20] => 3
    [21] => Referee
    [22] => Caleb Brown
    [23] => AR1
    [24] => J.R. Burnett
    [25] => AR2
    [26] => William (Bill) Steely
    [27] => No Fourth Position
    [28] => Empty
    [29] => No Fifth Position
    [30] => Empty
    [31] => No Note
    [32] =>
     */
}
?>
