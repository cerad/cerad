<?php
/* ==================================================
 * Wrap interface to the excel spreasheet processing
 */
namespace Cerad\Component\Excel;

class Excel
{
    public function __construct()
    {
        \PHPExcel_Cell::setValueBinder( new ExcelValueBinder() );
    }
    public function newSpreadSheet()
    {
        return new \PHPExcel();
    }
    public function newWriter($ss)
    {
        return \PHPExcel_IOFactory::createWriter($ss, 'Excel5');
    }
    public function getCoordForColRow($pColumn = 0, $pRow = 1)
    {
        return \PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow;
    }
    public function setCellHorizontalAllignment($ws, $cell, $alignment = '(center|left|right)') 
    {
        switch(strtolower($alignment)) {
            case "center":
                $align = \PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                break;
            case "left":
                $align = \PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                break;
            case "right":
                $align = \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                break;
            default:
                $align = \PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                break;
        }
        $ws->getStyle($cell)->getAlignment()->setHorizontal($align);
    }
    public function setColumnHorizontalAllignment($ws, $col, $rows, $alignment = '(center|left|right)') 
    {
        switch(strtolower($alignment)) {
            case "center":
                $align = \PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                break;
            case "left":
                $align = \PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                break;
            case "right":
                $align = \PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                break;
            default:
                $align = \PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                break;
        }
        $top = $this->getCoordForColRow($col,1);
        $bot = $this->getCoordForColRow($col,$rows);
        $range = $top . ':' . $bot;
        $ws->getStyle($range)->getAlignment()->setHorizontal($align);
    }
    protected function createReaderForFile($fileName,$readDataOnly = true)
    {
        // Most common case
        $reader = new \PHPExcel_Reader_Excel5();
        
        $reader->setReadDataOnly($readDataOnly);
        
        if ($reader->canRead($fileName)) return $reader;
 
        // Make sure have zip archive
        if (class_exists('ZipArchive')) 
        {
            $reader = new \PHPExcel_Reader_Excel2007();
        
            $reader->setReadDataOnly($readDataOnly);
        
            if ($reader->canRead($fileName)) return $reader;
     
        }
        
        // Note that csv does not actually check for a csv file
        $reader = new \PHPExcel_Reader_CSV();
        
        if ($reader->canRead($fileName)) return $reader;
        
        throw new Exception("No Reader found for $fileName");

    }
    public function load($fileName, $readDataOnly = true)
    {
        $reader = $this->createReaderForFile($fileName,$readDataOnly);

        return $reader->load($fileName);
    }
    public function loadx($file)
    {
        return \PHPExcel_IOFactory::load($file);
    }
}
 
?>