<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Cerad\Bundle\PersonBundle\DataTransformer\AYSO\RegionIDTransformer as Transformer;

/* ==================================================================
 * The transformer will yield AYSORxxxx
 * Use validation.yml for validation
 */
class RegionIDFormType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new Transformer();
        $builder->addModelTransformer($transformer);
        $builder->addViewTransformer ($transformer);
   }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label' => 'AYSO Region Number (1-1999)',
            'attr'  => array('size' => 4),
        ));
    }
    public function getParent() { return 'text'; }
    public function getName()   { return 'cerad_person_ayso_region_id'; }
}

?>
