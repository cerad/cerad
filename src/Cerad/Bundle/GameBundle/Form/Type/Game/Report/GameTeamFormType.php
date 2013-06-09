<?php
namespace Cerad\Bundle\GameBundle\Form\Type\Game\Report;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameTeamFormType extends AbstractType
{
    
    public function getName() { return 'cerad_game_report_game_team'; }
   
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\GameBundle\Entity\GameTeam'
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        $builder->add('report', new GameTeamReportFormType());  
    }
}
?>
