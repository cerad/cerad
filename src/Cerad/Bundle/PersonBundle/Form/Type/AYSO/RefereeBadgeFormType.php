<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/* ==================================================================
 * Use this to collect and partially validate a region number
 * The transformer will yield AYSORxxxx
 */
class RefereeBadgeFormType extends AbstractType
{   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
          //'invalid_message' => 'Unknown Region Number',
            
            'label'    => 'AYSO Referee Badge',
            'choices'  => $this->refereeBadgeChoices,
            'multiple' => false,
            'expanded' => false,
        ));
    }
    public function getParent() { return 'choice'; }
    public function getName()   { return 'cerad_person_ayso_referee_badge'; }
    
    protected $refereeBadgeChoices = array
    (
        'None'         => 'None',
        'Regional'     => 'Regional',
        'Intermediate' => 'Intermediate',
        'Advanced'     => 'Advanced',
        'National'     => 'National',
        'National2'    => 'National 2',
        'Assistant'    => 'Assistant',
        'U8Official'   => 'U8',
    );
}

?>
