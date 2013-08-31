<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\USSF;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/* ==================================================================
 * Use this to collect and partially validate a region number
 * The transformer will yield AYSORxxxx
 */
class RefereeUpgradingFormType extends AbstractType
{   
    public function getParent() { return 'choice'; }
    public function getName()   { return 'cerad_person_ussf_referee_upgrading'; }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
          
            'label'    => 'Working on Upgrade',
            'choices'  => $this->choices,
            'multiple' => false,
            'expanded' => false,
            
            'empty_value' => 'Working on Upgrade',
            'empty_data'  => null
            
        ));
    }    
    protected $choices = array
    (
        'No'       => 'No',
        'Yes'      => 'Yes',
        'SeeNotes' => 'See Notes',
   );    
}

?>
