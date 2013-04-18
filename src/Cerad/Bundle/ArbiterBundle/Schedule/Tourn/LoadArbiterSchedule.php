<?php
namespace Cerad\Bundle\ArbiterBundle\Schedule\Tourn;

use Cerad\Component\Excel\Loader as BaseLoader;

class LoadArbiterSchedule extends BaseLoader
{
    protected $record = array
    (
        'num'  => array('cols' => 'Game',        'req' => true),
        'date' => array('cols' => 'Date & Time', 'req' => true),
        'dow'  => array('cols' => 'Date & Time', 'req' => true, 'plus' => 2),
        'time' => array('cols' => 'Date & Time', 'req' => true, 'plus' => 3),
        
        'sport' => array('cols' => 'Sport & Level','req' => true),
        'level' => array('cols' => 'Sport & Level','req' => true, 'plus' => 1),
        
        'site'  => array('cols' => 'Site', 'req' => true),
        'home'  => array('cols' => 'Home', 'req' => true),
        'away'  => array('cols' => 'Away', 'req' => true),
      
        'referee' => array('cols' => 'Officials', 'req' => true),
        'ar1'     => array('cols' => 'Officials', 'req' => true, 'plus' => 1),
        'ar2'     => array('cols' => 'Officials', 'req' => true, 'plus' => 2),
    );
    protected function processItem($item)
    {
        $this->items[] = $item;
        return;
        print_r($item); die();
    }
}
?>
