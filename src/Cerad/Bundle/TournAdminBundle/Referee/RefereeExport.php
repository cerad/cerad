<?php
namespace Cerad\Bundle\TournAdminBundle\Referee;

use Cerad\Bundle\PersonBundle\DataTransformer\PhoneTransformer;

/* ============================================
 * Basic referee schedule exporter
 */
class RefereeExport
{
    protected $excel;
    protected $project;
    protected $persons;
    
    protected $personManager;
    protected $scheduleManager;
    
    protected $phoneTransformer;

    protected $counts = array
    (
        'Confirmed Referees'    => 0,
        'Might Referee'         => 0,
        'Ground Transportation' => 0,
        'Everyone'              => 0,
    );
    protected $widths = array
    (
        'PEID'         =>  5,
        'AYSOID'       => 10,
        'Games'        =>  5,
        'Last Name'    => 15,
        'First Name'   => 15,
        'Nick Name'    => 10,
        'Gender'       =>  5,
        'Email'        => 10,
        'Phone'        => 12,
        'Region'       =>  6,
        'Regon Desc'   => 25,
        'ST'           =>  4,
        'MY'           =>  5,
        'Safe Haven'   =>  5,
        'Ref Badge'    => 12,
        'Attend'       => 10,
        'Referee'      =>  5,
        'Ground Trans' =>  5,
        'Hotel'        => 20,
        'Will Assess'  => 20,
        'Want Assess'  => 20,
        'Volunteer'    => 10,
        'T-Shirt'      => 10,
    );
    
    public function __construct($excel,$project,$scheduleManager)
    {
        $this->excel   = $excel;
        $this->project = $project;
        
        $this->scheduleManager = $scheduleManager;
        $this->personManager   = $scheduleManager->personManager;
        
        $this->phoneTransformer = new PhoneTransformer();
    }
    protected function setHeaders($ws,$map)
    {
        $col = 0;
        foreach(array_keys($map) as $header)
        {
            $ws->getColumnDimensionByColumn($col)->setWidth($this->widths[$header]);
            $ws->setCellValueByColumnAndRow($col++,1,$header);
        }
        return 1;
    }
    protected function setRow($ws,$map,$person,&$row)
    {
        $row++;
        $col = 0;
        foreach($map as $propName)
        {
            $ws->setCellValueByColumnAndRow($col++,$row,$person[$propName]);
        }
        return $row;
    }
    /* ============================================
     * Master list of everyone
     */
    protected function generateProjectPersons($ws)
    {
        $map = array(
          //'PEID'         => 'id',
            'AYSOID'       => 'aysoid',
          //'Games'        => 'gameCount',
            'Last Name'    => 'lastName',
            'First Name'   => 'firstName',
            'Nick Name'    => 'nickName',
          //'Gender'       => 'gender',
            'Email'        => 'email',
            'Phone'        => 'phone',
            'Region'       => 'region',
          //'Regon Desc'   => 'regionDesc',
          //'ST'           => 'state',
         ///'MY'           => 'memYear',
         ///'Safe Haven'   => 'safeHaven',
            'Ref Badge'    => 'refBadge',
          //'Attend'       => 'attend',
          //'Referee'      => 'will_referee',
          //'Ground Trans' => 'ground_transport',
          //'Hotel'        => 'hotel',
            
            'Will Assess'  => 'assessing',
            'Want Assess'  => 'reqAssess',
            
          //'Volunteer'    => 'other_jobs',
          //'T-Shirt'      => 't_shirt_size'
        );
        $ws->setTitle('Everyone');
        $row = $this->setHeaders($ws,$map);
    
        $persons = $this->getPersons();
        foreach($persons as $person)
        {
            $this->setRow($ws,$map,$person,$row);
        }
        $this->counts['Everyone'] = $row - 1;
    }    
    /* ===================================================================
     * Originally from Zayso\NatGamesBundle\Component\Export\AccountExport
     */
    public function generate()
    {
        $excel = $this->excel;
        
        $ss = $excel->newSpreadSheet();
     
      //$this->generateConfirmed      ($ss->createSheet(1));
      //$this->generateMaybe          ($ss->createSheet(2));
      //$this->generateStates         ($ss->createSheet(3));
      //$this->generateGroundTransport($ss->createSheet(4));
        
      //$this->generateAssessments    ($ss->createSheet(5));
      //$this->generateAvailability   ($ss->createSheet(6));
        $this->generateProjectPersons ($ss->createSheet(0));
        
      //$this->generateCounts($ss->getSheet(0));
        
        // Output
        $ss->setActiveSheetIndex(0);
        $objWriter = $excel->newWriter($ss); // \PHPExcel_IOFactory::createWriter($ss, 'Excel5');

        ob_start();
        $objWriter->save('php://output'); // Instead of file name
        return ob_get_clean();
    }
     
    /* ====================================================
     * Return flattened array of people
     */
    protected function getPersons()
    {
        // Only process once
        if ($this->persons) return $persons;
        
        // Flat guys
        $items = array();
        
        $persons = $this->personManager->loadPersonsForProject($this->project);
        
        foreach($persons as $person)
        {
            $item = array();
            $item['id']        = $person->getId();
         
            $item['lastName']  = $person->getLastName();
            $item['firstName'] = $person->getFirstName();
            $item['nickName']  = $person->getNickName();
            $item['email']     = $person->getEmail();
            $item['phone']     = $this->phoneTransformer->transform($person->getPhone());   
             
          //$person['gender']    = $item->getGender();
          //$person['dob']       = $item->getDOB();
          //$person['gender']    = $person['gender'] . substr($person['dob'],0,4);
            
          //$org = $item->getOrgz();
          
          //$person['region']    = substr($org->getId(),4);
          //$person['regionDesc']= $org->getDesc2();
          //$person['state']     = $org->getState();
            
            // AYSO Stuff
            $league = $person->getVolunteerAYSO();
            $item['aysoid']    = substr($league->getIdentifier(),5);
            $item['region']    = substr($league->getLeague(),4);
            $item['memYear']   = $league->getMemYear();
            $item['safeHaven'] = $league->getCvpa();
            
            $item['refBadge']  = $person->getCertRefereeAYSO()->getBadge();
            
            // Plans
            $plan = $person->getPlan($this->project);
            $item['assessing'] = $plan->assessing;
            $item['reqAssess'] = $plan->reqAssess;
               
          //$person['gameSlots'] = $item->getGameRelsForProject($this->projectId);
          //$person['gameCount'] = count($person['gameSlots']);
            
            $items[] = $item;
        }
        // Maybe should do a local sort?
        
        // That was fun
        $this->persons = $items;
        return $items;
    }
}
?>
