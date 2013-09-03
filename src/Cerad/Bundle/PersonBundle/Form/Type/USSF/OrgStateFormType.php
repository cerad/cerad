<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\USSF;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/* ==================================================================
 * Use this to collect and partially validate a region number
 * The transformer will yield AYSORxxxx
 */
class OrgStateFormType extends AbstractType
{
    public function getParent() { return 'choice'; }
    public function getName()   { return 'cerad_person_ussf_org_state'; }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'label'    => 'State Certified In',
            'choices'  => $this->choices,
            'multiple' => false,
            'expanded' => false,
        ));
    }
    protected $choices = array
    (
        'USSF_AL' => 'Alabama',
        'USSF_AR' => 'Arkansas',
        'USSF_GA' => 'Gerogia',
        'USSF_LA' => 'Louisiana',
        'USSF_MS' => 'Mississippi',
        'USSF_TN' => 'Tennessee',
        'USSF_ZZ' => 'See Notes',
    );    
}

?>
