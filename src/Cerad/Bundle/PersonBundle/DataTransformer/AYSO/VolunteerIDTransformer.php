<?php
namespace Cerad\Bundle\PersonBundle\DataTransformer\AYSO;

use Symfony\Component\Form\DataTransformerInterface;

class VolunteerIDTransformer implements DataTransformerInterface
{
    public function transform($value)
    {   
        if (!$value) return null;

        if (substr($value,0,5) == 'AYSOV') return substr($value,5);

        return $value;
    }
    public function reverseTransform($value)
    {
        $id = preg_replace('/\D/','',$value);
        
        if (!$id) return null;
        
        // Should we check for 8 digits?  Should probably be done
        return 'AYSOV' . $id;
    }
}
?>
