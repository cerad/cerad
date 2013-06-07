<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Schedule\Referee;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;

class AssignPersonSubscriber implements EventSubscriberInterface
{
    private $factory;

    public function __construct(FormFactoryInterface $factory, $officials)
    {
        $this->factory   = $factory;
        $this->officials = $officials;
    }

    public static function getSubscribedEvents()
    {
        // Tells the dispatcher that we want to listen on the form.pre_set_data
        // event and that the preSetData method should be called.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $gamePerson = $event->getData();
        $form       = $event->getForm();

        if (!$gamePerson) return;
        
        // guid
        $personId = $gamePerson->getPerson();
        
        $statusPickList = array
        (
            'RequestAssignment'   => 'Request Assignment',
            'RequestRemoval'      => 'Request Removal',
            'AssignmentRequested' => 'Assignment Requested',
            'AssignmentApproved'  => 'Assignment Approved',
        );
        $officialsPickList = array();
        
        if ($personId) $emptyValue = null;
        else 
        {
            $emptyValue = 'Select Your Name';
            $statusPickList = array('RequestAssignment' => 'Request Assignment');
        }
        $matched = false;
        foreach($this->officials as $official)
        {
            $officialsPickList[$official->getId()] = $official->getName();
            if ($official->getId() == $personId) $matched = true;
        }
        if ($personId && !$matched)
        {
            // Someone not in officials is currently assigned
            $officialsPickList = array($personId => $gamePerson->getName());
            $emptyValue = false;
            $status = $gamePerson->getStatus();
            
            // Because of error in batch update
            if (!$status) $status = 'AssignmentRequested';
            
            if (isset($statusPickList[$status])) $statusDesc = $statusPickList[$status];
            else                                 $statusDesc = $status;
            
            $statusPickList = array($status => $statusDesc);
        }
        if ($personId && $matched)
        {
          //$officialsPickList = array($personId => $gamePerson->getName());
            $emptyValue = false;
            
            $statusPickList = array
            (
                'RequestRemoval'      => 'Request Removal',
                'AssignmentRequested' => 'Assignment Requested',
                'AssignmentApproved'  => 'Assignment Approved',
            );
        }
        $form->add($this->factory->createNamed('personx','choice', null, array(
            'label'         => 'Person',
            'required'      => false,
            'empty_value'   => $emptyValue,
            'empty_data'    => false,
            'auto_initialize' => false,
            'choices'       => $officialsPickList,
        )));
        
        // Mess with state
        $status = $gamePerson->getStatus();
        if (!$status) $status = 'RequestAssignment';
        $form->add($this->factory->createNamed('statusx','choice', null, array(
            'label'         => 'Status',
            'required'      => false,
            'empty_value'   => false,
            'empty_data'    => false,
            'choices'       => $statusPickList,
            'auto_initialize' => false,
        )));
        
        // Done
        return;
    }
}

class AssignPersonFormType extends AbstractType
{
    public function getName() { return 'schedule_referee_assign_person'; }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\GameBundle\Entity\GamePerson',
        ));
    }    
    public function __construct($officials)
    {
        $this->officials = $officials;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', 'text', array(
            'attr'      => array('size' => 10),
            'read_only' => true,
        ));
        $subscriber = new AssignPersonSubscriber($builder->getFormFactory(),$this->officials);
        $builder->addEventSubscriber($subscriber);
    }
}
class AssignFormType extends AbstractType
{   
    public function getName() { return 'schedule_referee_assign'; }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\GameBundle\Entity\Game',
        ));
    }    

    public function __construct($manager = null)
    {
        $this->manager = $manager;
    }
    protected $manager;
    protected $officials;
    
    public function setOfficials($officials)
    {
        $this->officials = $officials;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {     
        $builder->add('persons', 'collection', array('type' => new AssignPersonFormType($this->officials)));
    }
}
?>
