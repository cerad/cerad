<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

/* ==================================================================
 * One class to deal with eiher a basic string (AYSOR0498) or
 * and actual Entity.
 * 
 * Use the service id to select between the two
 * 
 * A third option would be to have a select list of know regions but don't think need a custom class for that
 */
class RegionFormType extends AbstractType
{   
    // Either entity or just the key/id
    protected $transformer;
    protected $name;
    
    public function __construct($name,$transformer)
    {
        $this->name        = $name;
        $this->transformer = $transformer;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'invalid_message' => 'Unknown Region Number',
            'label'           => 'AYSO Region Number (1-1999)',
            'attr'            => array('size' => 4),
            
            'constraints'     => array(
                new Assert\NotNull(array('message' => 'Region number is required')), 
                new Assert\Regex  (array('message' => 'Unknown region number', 'pattern' => '/^(AYSOR)?\d{4}$/'))),
            
            // This does not work because constraint are checked after transforming???
            // 'constraints'     => new Assert\Range(array('min' => 1, 'max' => 1999)),
        ));
    }
    public function getParent() { return 'text'; }
    public function getName()   { return $this->name; }
}

?>
