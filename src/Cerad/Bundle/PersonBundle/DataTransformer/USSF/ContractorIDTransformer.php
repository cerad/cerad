<?php
namespace Cerad\Bundle\PersonBundle\DataTransformer\USSF;

use Symfony\Component\Form\DataTransformerInterface;

class ContractorIDTransformer implements DataTransformerInterface
{
    protected $fake;
    
    public function __construct($fake = false)
    {
        $this->fake = $fake;
    }
    public function transform($value)
    {
        if (!$value) return null;

        if (substr($value,0,5) == 'USSFC') 
        {
            $id = substr($value,5);
            
            return sprintf('%s-%s-%s-%s',
                substr($id, 0,4),
                substr($id, 4,4),
                substr($id, 8,4),
                substr($id,12,4)
            );
            return substr($value,5);
        }

        return $value;
    }
    public function reverseTransform($value)
    {
        $id = preg_replace('/\D/','',$value);

        if (strlen($id) != 16 && $this->fake)
        {
            $id = '0000' . rand(1000,9999) . rand(1000,9999) . rand(1000,9999);
        }
        if (!$id) return null;
        
        return 'USSFC' . $id;
    }
}
?>
