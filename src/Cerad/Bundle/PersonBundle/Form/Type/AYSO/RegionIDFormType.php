<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

use Cerad\Bundle\PersonBundle\DataTransformer\AYSO\RegionIDTransformer;

/* ==================================================================
 * Use this to collect and partially validate a region number
 * The transformer will yield AYSORxxxx
 */
class RegionIDFormType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new RegionIDTransformer());
        $builder->addViewTransformer (new RegionIDTransformer());
   }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
          //'invalid_message' => 'Unknown Region Number',
            
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
    public function getName()   { return 'cerad_person_ayso_region_id'; }
}

?>
