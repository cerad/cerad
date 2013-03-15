<?php
namespace Cerad\Bundle\PersonBundle\DataTransformer\AYSO;

use Symfony\Component\Form\DataTransformerInterface;

class RegionIDTransformer implements DataTransformerInterface
{           
    public function transform($value)
    {
        if (!$value) return null;
        
        if (substr($value,0,5) == 'AYSOR') return (int)substr($value,5);

        return $value;
    }
    public function reverseTransform($value)
    {
        $id = (int)preg_replace('/\D/','',$value);
        
        if (!$id) return null;
        
        return sprintf('AYSOR%04u',$id);
    }
}
?>
