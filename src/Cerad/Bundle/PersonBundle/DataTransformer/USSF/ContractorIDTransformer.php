<?php
namespace Cerad\Bundle\PersonBundle\DataTransformer\USSF;

use Symfony\Component\Form\DataTransformerInterface;

class ContractorIDTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        if (!$value) return null;

        if (substr($value,0,5) == 'USSFC') return substr($value,5);

        return $value;
    }
    public function reverseTransform($value)
    {
        $id = preg_replace('/\D/','',$value);
        
        if (!$id) return null;
        
        return 'USSFC' . $id;
    }
}
?>
