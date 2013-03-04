<?php
namespace Cerad\Bundle\ScheduleBundle\Schedule\Import;

/* =====================================================================
 * isRowGame (everything is available, still possible to be append)
 * isRowGameAppend (numeric number and at least one field)
 * isRowStatus
 * isRowComment
 * isRowCommentAppend not status or comment, something in first and everything else blank, previous comment
 * 
 * isRowOther
 *   isRowBlank
 *   isRowHeader
 *   isRowFooter
 * 
 * Tricky part is distingusing between isRowCommentAppend and isRowGame or isRowGameAppend
 * If have comments then know it is not a isRowGameAppend
 * If have officials then know it is not isRowGameAppend
 * 
 * Assume each game has at least one official?
 * 
 */
class ImportSchedulePortXLS extends ImportScheduleBase
{
    protected function isRowBlank($row)
    {
        foreach($row as $col)
        {
            if (strlen($col)) return false;
        }
        return true;
    }
    protected function isRowHeader($row)
    {
        $names = array('Game','Date & Time','Sport & Level','Site','Home','Away');
        foreach($names as $name)
        {
            if (array_search($name,$row) === false) return false;
        }
        return true;
    }
    protected function isRowFooter($indexes,$row)
    {   
        $names = array('page_footer_date','page_footer_by','page_footer_page');
        foreach($names as $name)
        {
            if (!$row[$indexes[$name]]) return false;
        }
        return true;
    }
    protected function isRowPossibleGame($indexes,$row)
    {   
        // Must have a non-zero number
        if (!is_numeric($row[$indexes['num']])) return false;
      
        $names = array('date_time','sport_level','site','home_team','away_team');
        foreach($names as $name)
        {
            if (!$row[$indexes[$name]]) return false;
        }
        return true;
    }
    /* ---------------------------------------------------------
     * Returns set of indexes if row is an header row
     * Account for score / no score which have some different offsets
     */
    protected function extractIndexes($row)
    {
        if (!$this->isRowHeader($row)) return null;
       
        $indexes = array();
            
        $indexes['first'] = array_search('Game',$row);
        $indexes['num']   = array_search('Game',$row);

        $indexes['date_time'] = array_search('Date & Time',$row);
                
        $indexes['sport_level'] = array_search('Sport & Level',$row);

        $indexes['site']      = array_search('Site',$row);
        $indexes['home_team'] = array_search('Home',$row);
        $indexes['away_team'] = array_search('Away',$row);
        
        // Just to be sure
        if (!$indexes['date_time'] || !$indexes['sport_level'] || !$indexes['site']) return null;
        
        $indexes['bill_to'] = array_search('Bill-To',$row);
        $indexes['score']   = array_search('Score',  $row);
        
        // For referee info, no headers
        $indexes['official_pos']    = $indexes['first']       + 1;
        $indexes['official_name']   = $indexes['date_time']   + 1;
        $indexes['official_rank']   = $indexes['sport_level'] + 2;
        $indexes['official_email']  = $indexes['bill_to']     + 1; // Line by itself
        $indexes['official_phone1'] = $indexes['bill_to']     + 1;
        $indexes['official_phone2'] = $indexes['site']        + 1;
        
        $indexes['official_status'] = $indexes['away_team'] + 1;
        $indexes['official_fee']    = $indexes['score']     + 1;
 
        $indexes['page_footer_date'] = $indexes['first'];
        $indexes['page_footer_by']   = $indexes['sport_level'] + 2;
        $indexes['page_footer_page'] = $indexes['score'];
        
        if (!$indexes['score'])
        {
            $indexes['official_status']  = $indexes['home_team'] + 3;
            $indexes['official_fee']     = $indexes['away_team'] + 2;
            $indexes['page_footer_page'] = $indexes['away_team'] + 1;
        }
        
        return $indexes;
    }
    // Assume that check for game is already done
    protected function extractGame($indexes,$row)
    {   
        $game = array();
        $names = array('num','date_time','sport_level','site','home_team','away_team','bill_to');
        foreach($names as $name)
        {
            $game[$name] = $row[$indexes[$name]];
        }
        return $game;
    }
    protected function extractOfficial($indexes,$row)
    {
        $names = array(
            'official_pos','official_name','official_rank',
            'official_phone1','official_phone2','official_status','official_fee'
        );
        $allBlank = true;
        $official = array();
        foreach($names as $name)
        {
            // Could be a partial row
            $value = $row[$indexes[$name]];
            
            if (strlen($value)) $allBlank = false;
            
            $official[substr($name,9)] = $value;
        }
        $official['email'] = null;
        if (!$allBlank) return $official;
        
        return null;
    }
    protected function extractOfficialEmail($indexes,$row)
    {
        $email = $row[$indexes['official_email']];
        if (!$email) return null;

        $names = array(
            'official_pos','official_name','official_rank','official_phone2','official_status',
        );
        foreach($names as $name)
        {
            // All need to be blank
            if (strlen($row[$indexes[$name]])) return null;
        }
        return $email;
    }
    /* --------------------------------------------------------
     * This is called when in the process of reading in a game
     * 
     * Return done = 0 if the game is not yet done
     * Return done = 1 if reach something that terminates a game
     * Return done = 2 if the row actually starts a new game
     */
    protected function extractNextGameRow($indexes,$row,&$game)
    {
        // Certain types of rows will always terminate a game
        if ($this->isRowBlank ($row)) return 1;
        if ($this->isRowHeader($row)) return 1;
        if ($this->isRowFooter($indexes,$row)) return 1;
        
        // The first column is key to a bunch of majic
        $first = $row[$indexes['first']];
        
        // Is the row a status?
        if (substr($first,0,22) == '*** This game has been') // *** This game has been CANCELED ***
        {
            $status = substr($first,23,6);
            switch($status)
            {
                case 'CANCEL': $status = 'Canceled';  break; 
                case 'FORFEI': $status = 'Forfeited'; break; 
            }
            $game['status'] = $status;
             
            // Status always ends a game?
            return 1;
        }
        // Does the row start a comment?
        if (strlen($first) && $first[0] == '[')
        {
            $game['comments'][] = $first;
            return 0;
        }
        // Multi line comment
        if (strlen($first) && !is_numeric($first))
        {
            $i = count($game['comments']);
            if ($i)
            {
                $game['comments'][$i - 1] . ' ' . $first;
            }
            else die('Expected multi line comment');
            return 0;
        }
        // Process officials
        $email = $this->extractOfficialEmail($indexes,$row);
        if ($email)
        {
            $game['officials'][count($game['officials']) - 1]['email'] = $email;
            return 0;
        }
        $official = $this->extractOfficial($indexes,$row);
        if ($official)
        {
            if ($official['pos'])
            {
                $game['officials'][] = $official;
                return 0;
            }
            // Partial official ?
            return 0;
        }
        if ($this->isRowPossibleGame($indexes,$row))
        {
            // Possible ran into next game if current game ended in comment
            if (count($game['comments' ])) return 2;
            if (count($game['officials'])) return 2;
        }
        // All this is left should be a cat game row
        $allBlank = true;
        $gamex = $this->extractGame($indexes,$row);
        foreach($gamex as $name => $value)
        {
            if (strlen($value))
            {
                $allBlank = false;
                $game[$name] .= '~~~' . $value;
            }
        }
        if (!$allBlank) return 0;
        
        print_r($game);
        print_r($row);
        die("Could not process row\n");
    }
    /* -----------------------------------------------------------------
     * If the row starts a game then extract and return basic game info
     */
    protected function extractFirstGameRow($indexes,$row)
    {
        if (!$this->isRowPossibleGame($indexes,$row)) return null;
        
        // These should all have values
        $game = $this->extractGame($indexes,$row);
        
         // Optional scores
        $game['home_score'] = null;
        $game['away_score'] = null;
        if ($indexes['score'])
        {
            $scores = $row[$indexes['score']];
            $info = explode('-',$scores);
            if (count($info) == 2) 
            {
                $game['home_score'] = (int)$info[0];
                $game['away_score'] = (int)$info[1];
           }
        }
        // Last few bits
        $game['officials'] = array();
        $game['comments']  = array();
        
        return $game;
    }
    /* ------------------------------------------------------------
     * Cycle through the array of rows
     * Pull out game number and project
     * Then extract referee information
     */
    protected function processRows($rows)
    {    
        $indexes = null;
        $game    = null;
        
        // Do it in one pass
        foreach($rows as $row)
        {
            // Get rid of all the trim nonsense
            array_walk($row, create_function('&$val', '$val = trim($val);'));
            
            // Need indexes
            if (!$indexes) 
            {
                $indexes = $this->extractIndexes($row);
                continue;
            }
            // Slide down to game row
            if (!$game)
            {
                $game = $this->extractFirstGameRow($indexes,$row);
                continue;
            }
            // Little bit tricky here
            $gameDone = $this->extractNextGameRow($indexes,$row,$game);
            if ($gameDone)
            {   
                $this->ImportGame($game);

                // Might actually be the start of a new game
                if ($gameDone == 2) $game = $this->extractFirstGameRow($indexes,$row);
                else                $game = null;
            }
        }
        if ($game) $this->importGame($game);
    }
    /* =======================================================================
     * Main entry point
     */
    public function importFile($params,$rows)
    {
        $this->params = $params;
        
        $this->gameManager->getEventManager()->addEventSubscriber($this);
        
        $this->results = new ImportScheduleResults();
        
        $this->results->inputFileName  = $params['inputFileName'];
        $this->results->clientFileName = $params['clientFileName'];
        $this->results->totalGameCount = 0;
        
        if ($params['output'] == 'Post') $this->persistFlag = true;
        
        // Probably want to loop here but maybe not
        // Sometimes need to peek ahead
        $this->processRows($rows);
        
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
        
        // Find the level to get the domain_sub so we can get the project
        $props = array();
        $props['sport']     = $this->params['sport'];
        $props['domain']    = $this->params['domain'];   // AKA Arbiter Group
        $props['domainSub'] = null;
        $props['name']      = $game['sport_level'];
        
        $level = $this->levelManager->processEntity($props,$this->persistFlag);

        if (!$level)
        {
            print_r($game); die(' *** NO LEVEL ***');
            return;
        }

        // Now find the project
        $props = array();
        $props['sport']     = $this->params['sport'];
        $props['domain']    = $this->params['domain'];
        $props['domainSub'] = $level->getDomainSub();
        $props['season']    = $this->params['season'];
        
        $project = $this->projectManager->processEntity($props,$this->persistFlag);
        if (!$project)
        {
            print_r($game); die(' *** NO PROJECT ***');
            return;
        }
        
        // Now can get the existing game
        $num = (int)str_replace('~','',$game['num']);
        $gamex = $this->gameManager->loadGameForProjectNum($project,$num);
        if (!$gamex)
        {
            print_r($game); die(' *** NO EXISTING GAME ***');
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
                
                $person->setRole  ($official['pos']);
                $person->setName  ($name);
                $person->setEmail ($official['email' ]);
                $person->setStatus($official['status']);
                
                // Is a bit of a pain, want cell phone if available
                $haveCellPhone = false;
                $phone1 = $official['phone1'];
                if ($phone1 && $phone1[0] == 'C') 
                {
                    $person->setPhone($phone1);
                    $haveCellPhone = true;
                }
                else
                {
                    $phone2 = $official['phone2'];
                    if ($phone2 && $phone2[0] == 'C')
                    {
                        $person->setPhone($phone2);
                        $haveCellPhone = true;
                    }
                }
                if (!$haveCellPhone)
                {
                    if ($phone1) $person->setPhone($phone1);
                    else 
                    {
                        if ($phone2) $person->setPhone($phone2);
                    }
                }
            }
            else
            {
                // Deal with this later
            }
        }
        // Flush any changes
        $this->flush();
    }
}
?>
