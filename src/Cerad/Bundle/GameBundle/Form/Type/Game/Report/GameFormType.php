<?php
namespace Cerad\Bundle\GameBundle\Form\Type\Game\Report;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GameFormType extends AbstractType
{
    
    public function getName() { return 'cerad_game_report_game'; }
   
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\GameBundle\Entity\Game'
        ));
    }
    protected function addText($builder,$name,$label,$size=20)
    {
       $builder->add($name,'text', array
       (
           'read_only' =>  true, 
           'required'  => false,
           'label'     => $label,
           'attr'      => array('size' => $size),

        )); 
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Don't think need these
        /*
        $this->addText($builder,'num',      'Number',6);
        $this->addText($builder,'role',     'Role',  6);
        $this->addText($builder,'fieldDesc','Field',12);
        $this->addText($builder,'levelDesc','Level',20);
        */
        
        // Status is the only game field changed
        $builder->add('status', 'choice', array('label' => 'Status',
        'choices' => array
        (
            'Normal'     => 'Normal',
            'InProgress' => 'In Progress',
            'Played'     => 'Played',
            
            'Forfeited'  => 'Forfeited', 
            'Cancelled'  => 'Cancelled', 
            'Suspended'  => 'Suspended', 
            'Terminated' => 'Terminated',
            'StormedOut' => 'Stormed Out',
            ),
        ));
        
        $builder->add('report',   new GameReportFormType());
        $builder->add('homeTeam', new GameTeamFormType());
        $builder->add('awayTeam', new GameTeamFormType());
       
    }
}
?>
