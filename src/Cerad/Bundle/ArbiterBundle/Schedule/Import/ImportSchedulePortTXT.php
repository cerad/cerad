<?php
namespace Cerad\Bundle\ScheduleBundle\Schedule\Import;

/* =====================================================================
 * Reading a text/csv file is very fast
 * Fragile because the commas are not escaped
 * 
 */
class ImportSchedulePortTXT extends ImportScheduleBase
{
    /* =======================================================================
     * Process one row from the stream
     */
    protected $game;
    
    public function processRow($row)
    {
        $count = count($row);
        if (!$count || !$row[0]) return;

        $first = $row[0];
        
        if (($first[0] == '[') || (strpos($first,'***') !== false))
        {
            // Process comment, could be a status
            // 1 Both coaches agreed.*** This game has been CANCELED. ***
            // 1 en SUSPENDED. ***
            return;
        }
        switch($count)
        {
            // Referee email ends up all by itself in the first column
            case 1: 
                switch($first)
                {
                    case 'Assigner':
                    case 'Master Game Schedule':
                        return;
                }
                if (strpos($first,'@'))
                {
                    $this->game['officials'][count($this->game['officials']) - 1]['email'] = $first;
                    return;
                }
                //echo sprintf("%d %s\n",$count,$row[0]);
                
                return;
            
            // Header or game without extra commas
            case 8:       
                // 8 ^1301007^ ~2/9/2013 Sat9:00 AM~ $AHSAAJamboree$ &E. Limestone HS& *East Limestone HS stadium* +E. Limestone HS+ =Catholic HS= +-+

                // Header
                // 8 ^Game^ ~Date & Time~ $Sport & Level$ &Bill-To& *Site* +Home+ =Away= +Score+
                if (($row[0] == 'Game') && ($row[1] == 'Date & Time')) return;
                    
                // Gameid should be numeric
                if (!is_numeric($first)) return;
                $num = (int)$first;
                if (!$num) return;
                
                if ($this->game) 
                {
                    $this->importGame($this->game);
                    $this->game = null;
                }
                $this->game = array(
                    'num'         => $num,
                    'date_time'   => $row[1],
                    'sport_level' => $row[2],
                    'bill_to'     => $row[3],
                    'site'        => $row[4],
                    'home_team'   => $row[5],
                    'away_team'   => $row[6],
                    'score'       => $row[7],
                    'officials'   => array(),
                );
                return;
                
            // Game with an extra comma in bill_to
            case 9:       
                // Problem here is that the bill_to value has a comma MSSL, Mark Tillman
                // Commas in teams and locations will cause issues
                // 9 ^401^ ~1/26/2013 Sat11:30 AM~ $MSSLMS-B$ &MSSL& * Mark Tillman* +Bell Mountain Park+ =Mountain Gap Coed= +Buckhorn Gold+ _6-0_
                    
                // Gameid should be numeric
                if (!is_numeric($first)) return;
                $num = (int)$first;
                if (!$num) return;
                
                if ($this->game) 
                {
                    $this->importGame($this->game);
                    $this->game = null;
                }
                $this->game = array(
                    'num'         => $num,
                    'date_time'   => $row[1],
                    'sport_level' => $row[2],
                    'bill_to'     => $row[3] . ', ' . $row[4],
                    'site'        => $row[5],
                    'home_team'   => $row[6],
                    'away_team'   => $row[7],
                    'score'       => $row[8],
                    'officials'   => array(),
                );
                return;
                
            // Official
            case 7: 
                // 7 ^Referee^ ~Gene Uhl~    $28$ &C: 256-714-9284& *               * +Accepted+ =$0.00=
                // 7 ^AR1^     ~Shannon Uhl~ $28$ &C: 256-714-9379& *H: 256-701-6829* +Accepted+ =$0.00=
                // 7 ^AR2^     ~Gary Hall~    $0$ &C: 256-679-7554& *W: 256-876-7411* +Accepted+ =$0.00=
                
                // 7 ^Wednesday^ ~ February 27~ $ 2013$ & 9:35 AM& *Created by ArbiterSports.com* +Page 1 of+ =27=
                if ($row[4] == 'Created by ArbiterSports.com') return;
                
                $official = array
                (
                    'role'   => $row[0],
                    'name'   => $row[1],
                    'phone'  => $row[3],
                    'email'  => null,
                    'status' => $row[5],
                );
                if (!$official['phone'] || $official['phone'][0] != 'C')
                {
                    if ($row[4] && $row[4][0] == 'C') $official['phone'] = $row[4];
                }
                $this->game['officials'][] = $official;
                return;
        }
    }
    /* =======================================================================
     * Process one row from the stream
     */
    public function processRowDebug($row)
    {
        $count = count($row);
        if (!$count || !$row[0]) return;

        if ($row[0][0] == '[')
        {
            // Process comment
            return;
        }
        switch(count($row))
        {
            case 1: 
                $value = $row[0];
                if (!$value) return;
                // Comment, status, referee email, general stuff
                return;
                break;
                
            case 2: 
                // More comments
                //echo sprintf("%d %s ~~~%s~~~\n",$count,$row[0],$row[1]);
                return;
                
            case 3: 
                // More comments 
                // 3 ^^^Total^^^:  ~~~$114~~~ $$$191.84$$$
                //echo sprintf("%d ^^^%s^^^ ~~~%s~~~ $$$%s$$$\n",$count,$row[0],$row[1],$row[2]);
                return;
                
            case 4: 
                // More comments 
                // 3 ^^^Total^^^:  ~~~$114~~~ $$$191.84$$$
                // echo sprintf("%d ^^^%s^^^ ~~~%s~~~ $$$%s$$$ &&&%s&&&\n",$count,$row[0],$row[1],$row[2],$row[3]);
                return;
                
            case 5: 
                // More comments / report?  Need to verify game number
                // 3 ^^^Total^^^:  ~~~$114~~~ $$$191.84$$$
                //echo sprintf("%d ^^^%s^^^ ~~~%s~~~ $$$%s$$$ &&&%s&&& ***%s***\n",$count,
                //        $row[0],$row[1],$row[2],$row[3],$row[4]);
                return;
                
            case 6: 
                // NONE!
                echo sprintf("%d ^^^%s^^^ ~~~%s~~~ $$$%s$$$ &&&%s&&& ***%s*** +++%s+++\n",$count,
                        $row[0],$row[1],$row[2],$row[3],$row[4],$row[5]);
                return;
                
            case 7: 
                // 7 ^Referee^ ~Gene Uhl~    $28$ &C: 256-714-9284& *               * +Accepted+ =$0.00=
                // 7 ^AR1^     ~Shannon Uhl~ $28$ &C: 256-714-9379& *H: 256-701-6829* +Accepted+ =$0.00=
                // 7 ^AR2^     ~Gary Hall~    $0$ &C: 256-679-7554& *W: 256-876-7411* +Accepted+ =$0.00=
                
                // 7 ^Wednesday^ ~ February 27~ $ 2013$ & 9:35 AM& *Created by ArbiterSports.com* +Page 1 of+ =27=
                if ($row[4] == 'Created by ArbiterSports.com') return;
                
                switch($row[0])
                {
                    case 'Referee':
                    case 'AR1':
                    case 'AR2':
                    case "Ass't Referee":
                        return;
                }
                echo sprintf("%d ^%s^ ~%s~ $%s$ &%s& *%s* +%s+ =%s=\n",$count,
                        $row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6]);
                return;
                
            case 8: 
                // And we have some comments that stretch this far
                    
                // 8 ^1301007^ ~2/9/2013 Sat9:00 AM~ $AHSAAJamboree$ &E. Limestone HS& *East Limestone HS stadium* +E. Limestone HS+ =Catholic HS= +-+

                // Header
                // 8 ^Game^ ~Date & Time~ $Sport & Level$ &Bill-To& *Site* +Home+ =Away= +Score+
                if (($row[0] == 'Game') && ($row[1] == 'Date & Time')) return;
                    
                // Gameid should be numeric
                $gameId = $row[0];
                if (is_numeric($gameId)) return;

                echo sprintf("%d ^%s^ ~%s~ $%s$ &%s& *%s* +%s+ =%s= +%s+\n",$count,
                    $row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7]);
                return;
 
            case 9:
                // Problem here is that the bill_to value has a comma MSSL, Mark Tillman
                // Commas in teams and locations will cause issues
                // 9 ^401^ ~1/26/2013 Sat11:30 AM~ $MSSLMS-B$ &MSSL& * Mark Tillman* +Bell Mountain Park+ =Mountain Gap Coed= +Buckhorn Gold+ _6-0_
                $gameId = $row[0];
                echo sprintf("%8d %s\n",(int)$gameId,$row[3]);
                if (is_numeric($gameId)) return;
                
                echo sprintf("%d ^%s^ ~%s~ $%s$ &%s& *%s* +%s+ =%s= +%s+ _%s_\n",$count,
                    $row[0],$row[1],$row[2],$row[3],$row[4],$row[5],$row[6],$row[7],$row[8]);
                return;
                         
          //default: return;
        }
        // Nothing over 9 at least once [ rows are filtered out
        echo sprintf("%d %s\n",$count,$row[0]);
    }
    /* =======================================================================
     * Main entry point
     */
    public function importFile($params,$fp)
    {
        if (!$fp) throw new \Exception('ImportSchedulePortTXT with no file pointer');
        
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
            
            $this->processRow($row);
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
    public function importGame($game)
    {
        if (!$game) return;
        
        $this->results->totalGamesCount++;
        
      //print_r($game); return; // die();
        
        // Find the level to get the domain_sub so we can get the project
        $props = array();
        $props['sport']     = $this->params['sport'];
        $props['domain']    = $this->params['domain'];   // AKA Arbiter Group
        $props['domainSub'] = null;
        $props['name']      = $game['sport_level'];
        
        // Must exist
        $level = $this->levelManager->processEntity($props,false);

        if (!$level)
        {
            $this->results->noLevelCount++;
          //print_r($game); die(' *** NO LEVEL ***');
            return;
        }

        // Now find the project
        $props = array();
        $props['sport']     = $this->params['sport'];
        $props['domain']    = $this->params['domain'];
        $props['domainSub'] = $level->getDomainSub();
        $props['season']    = $this->params['season'];
        
        $project = $this->projectManager->processEntity($props,false);
        if (!$project)
        {
            // Probably should not happen
            $this->results->noProjectCount++;
          //print_r($game); die(' *** NO PROJECT ***');
            return;
        }
        
        // Now can get the existing game
        $gamex = $this->gameManager->loadGameForProjectNum($project,$game['num']);
        if (!$gamex)
        {
            $this->results->noGameCount++;
          //print_r($game); die(' *** NO EXISTING GAME ***');
            return;
        }
        /* =======================================================
         * TODO: This is where we can update some stuff on the game
         * Though the slot report is probably better
         */
        
        // Only update the referee stuff
        foreach($game['officials'] as $slot => $official)
        {
          //print_r($official); die();
            $person = $gamex->getPersonForSlot($slot + 1);
            if ($person)
            {
                //$this->propHasChanged = false;
                //$person->addPropertyChangedListener($this);
                
                // Should probably clear out empty slots completely
                $name = $official['name'];
                if (strpos($name,'____') === 0) $name = '';
                
                $person->setRole  ($official['role']);
                $person->setName  ($name);
                $person->setEmail ($official['email' ]);
                $person->setPhone ($official['phone' ]);
                $person->setStatus($official['status']);                
            }
            else
            {
                // Deal with this later
            }
        }
        // Might want to handle a change in slot count here as well
        
        // Flush any changes
        $this->flush();
    }
}
?>
