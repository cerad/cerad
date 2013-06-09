<?php
namespace Cerad\Bundle\GameBundle\Form\Type\Game\Report;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameTeamReportFormType extends AbstractType
{
    
    public function getName() { return 'cerad_game_report_game_team_report'; }
   
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\GameBundle\Entity\GameTeamReport',
            'required'   => false,
            'attr'       => array('size' => 4),

        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {        
        // Status Workflow
        $builder->add('goalsScored',   'integer', array('attr' => array('size' => 4)));
        $builder->add('sportsmanship', 'integer', array('attr' => array('size' => 4)));
        $builder->add('fudgeFactor',   'integer', array('attr' => array('size' => 4)));
        
        $builder->add('playerWarnings', 'integer', array('attr' => array('size' => 4)));
        $builder->add('playerEjections','integer', array('attr' => array('size' => 4)));
        
        $builder->add('coachWarnings', 'integer', array('attr' => array('size' => 4)));
        $builder->add('coachEjections','integer', array('attr' => array('size' => 4)));

        $builder->add('pointsEarned', 'integer',  array('attr' => array('size' => 4),'read_only' =>  true));


    }
}
?>
