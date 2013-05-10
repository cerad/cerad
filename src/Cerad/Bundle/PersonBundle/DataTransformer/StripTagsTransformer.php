<?php
namespace Cerad\Bundle\PersonBundle\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class StripTagsTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        //echo get_class($value); die(' WTF Strip Transform');
        return $value;
    }
    public function reverseTransform($value)
    {
       //echo get_class($value); die(' WTF Strip Reverse');
         return strip_tags($value);
    }
}
?>
