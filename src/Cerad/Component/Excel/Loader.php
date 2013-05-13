<?php
namespace Cerad\Component\Excel;

class Loader
{
    protected $excel;
    protected $items  = array();
    protected $errors = array();
    
    protected $record = array(
      // 'region'     => array('cols' => 'Region',         'req' => true,  'default' => 0),
    );
    protected $map = array();
    
    public function __construct()
    {
        $this->excel = new Reader();
    }
    protected function processDataRow($row)
    {
        $item = array();
        foreach($this->record as $name => $params)
        {
            if (isset($params['default'])) $default = $params['default'];
            else                           $default = null;
            $item[$name] = $default;
        }
        foreach($row as $index => $value)
        {
            if (isset($this->map[$index]))
            {
                $name = $this->map[$index];
                $item[$name] = trim($value);
            }
        }
        return $item;
    }
    protected function processHeaderRow($row)
    {
        $found  = array();
        $record = $this->record;
        foreach($row as $index => $colName)
        {
            $colName = trim($colName);
            foreach($record as $name => $params)
            {
                if (is_array($params['cols'])) $cols = $params['cols'];
                else                           $cols = array($params['cols']);
                foreach($cols as $col)
                {
                    if ($col == $colName)
                    {
                        if (isset($params['plus'])) $plus = $params['plus'];
                        else                        $plus = 0;
                        
                        $this->map[$index + $plus] = $name;
                        $found[$name] = true;
                    }
                }
            }
        }

        // Make sure all required attributes found
        foreach($record as $name => $params)
        {
            if (isset($params['req']) && $params['req'])
            {
                if (!isset($found[$name]))
                {
                    if (is_array($params['cols'])) $cols = $params['cols'];
                    else                           $cols = array($params['cols']);
                    $cols = implode(' OR ',$cols);
                    $this->errors[] = "Missing $cols";
                }
            }
        }
    }
    public function load($inputFileName, $worksheetName = null)
    {
        $reader = $this->excel->load($inputFileName);

        if ($worksheetName) $ws = $reader->getSheetByName($worksheetName);
        else                $ws = $reader->getSheet(0);
        
        $rows = $ws->toArray();
        
        $header = array_shift($rows);
        
        $this->processHeaderRow($header);
        
        // Insert each record
        foreach($rows as $row)
        {
            $item = $this->processDataRow($row);
            
            $this->processItem($item);
        }
        return $this->items;
    }
    protected function processItem($item)
    {
        print_r($item); die("\n");
    }
    protected function processTime($time)
    {
        return \PHPExcel_Style_NumberFormat::toFormattedString($time,'hh:mm:ss');
    }
}
?>
