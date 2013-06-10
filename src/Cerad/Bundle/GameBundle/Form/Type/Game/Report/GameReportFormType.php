<?php
namespace Cerad\Bundle\GameBundle\Form\Type\Game\Report;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameReportFormType extends AbstractType
{
    
    public function getName() { return 'cerad_game_report_game_report'; }
   
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\GameBundle\Entity\GameReport'
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        // Status Workflow
        $builder->add('status', 'choice', array('label' => 'Status',
        'choices' => array
        (
            'Pending'   => 'Pending',
            'Submitted' => 'Submitted',
            'Verified'  => 'Verified',
            'Clear'     => 'Clear',
           ),
        ));
        $builder->add('text','textarea',array('label' => 'Text', 'required' => false, 
            'attr' => array('rows' => 4, 'cols' => 78, 'wrap' => 'hard', 'class' =>'textarea')));

    }
}
?>
