<?php
namespace Cerad\Component\Excel;

/* ==============================================================================
 * Want to pass either DateTime object with flag of some sort or
 * Pass explicit ~~~ DATE ~~~ Date format
 * 
 * Probably want to extend from ValueBinder and not the advanced binder so other issues
 * Will not cause problems
 * 
 * Know score 2-2 will cause issues
 */
class ExcelValueBinder extends \PHPExcel_Cell_AdvancedValueBinder
{
    /* ------------------------------------------
     * Checks for mm/dd/yyyy
     * Returns yyyy-mm-dd if found
     */
    protected function isMyDate($value)
    {
        if (strlen($value) != 10) return false;
        
        $parts = explode('/',$value);
        if (count($parts) != 3) return false;
        
        foreach($parts as $part)
        {
            if (!is_numeric($part)) return false;
        }
        return $parts[2] . '-' . $parts[0] . '-' . $parts[1];
    }
    /* ------------------------------------------
     * Checks for hh:mm AM or PM
     * Returns HH:MM if found
     */
    protected function isMyTime($value)
    {
        if (strlen($value) != 8) return false;
        
        $parts = explode(' ',$value);
        if (count($parts) != 2) return false;
        
        switch($parts[1])
        {
            case 'AM': $offset =  0; break;
            case 'PM': $offset = 12; break;
            default: return false;
        }
        $parts = explode(':',$parts[0]);
        if (count($parts) != 2) return false;
       
        foreach($parts as $part)
        {
            if (!is_numeric($part)) return false;
        }
        $hours   = (int)$parts[0] + $offset;
        $minutes = (int)$parts[1];
        
        return sprintf('%02d:%02d',$hours,$minutes);
    }
    // The binder
    public function bindValue(\PHPExcel_Cell $cell, $value = null)
    {
        // sanitize UTF-8 strings
        if (is_string($value)) 
        {
            $value = \PHPExcel_Shared_String::SanitizeUTF8($value);
        }
        if ($value instanceof \PHPExcel_RichText) return parent::bindValue($cell, $value);

        // Find out data type
        $dataType = parent::dataTypeForValue($value); 
        if ($dataType !== \PHPExcel_Cell_DataType::TYPE_STRING) return parent::bindValue($cell, $value);
        
        // Process Date
        $date = $this->isMyDate($value);
        if ($date)
        {
            // Check for datetime, e.g. '2008-12-31', '2008-12-31 15:59', '2008-12-31 15:59:10'
            if (($d = \PHPExcel_Shared_Date::stringToExcel($date)) !== false) 
            {    
                // Convert value to number
                $cell->setValueExplicit($d, \PHPExcel_Cell_DataType::TYPE_NUMERIC);
                
                $formatCode = 'mm/dd/yyyy';
				
                $cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat()->setFormatCode($formatCode);
                return true;
            }
        }
        // Process time
        $time = $this->isMyTime($value);
        if ($time)
        {
          //die($value . ' ' . $time);
            
            list($h, $m) = explode(':', $time);
            $days = $h / 24 + $m / 1440;
            
            // Convert value to number
            $cell->setValueExplicit($days, \PHPExcel_Cell_DataType::TYPE_NUMERIC);
            
            // Set style
            $formatCode = 'h:mm AM/PM'; // '5 PM'
            $cell->getParent()->getStyle( $cell->getCoordinate() )->getNumberFormat()->setFormatCode($formatCode);
            return true;
       }
        return parent::bindValue($cell, $value);
    }
}
?>
