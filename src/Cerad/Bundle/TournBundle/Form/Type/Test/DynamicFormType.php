<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Test;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DynamicFormType extends AbstractType
{
    public function getName() { return 'cerad_tourn_test_dynamic'; }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('gender', 'choice', array(
            'choices'   => array('m' => 'Male', 'f' => 'Female'),
            'required'  => false,
        ));
        $builder->addEventSubscriber(new DynamicFormListener($builder->getFormFactory()));
    }
}
class DynamicFormListener implements EventSubscriberInterface
{
    private $factory;
    
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT   => 'preSubmit',
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }
    public function preSetData(FormEvent $event)
    {
        // Don't need
        return;
    }
    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $gender = $data['gender'];
        if (!$gender) return;
        
        /* =================================================
         * All we need to do is to replace the choice with one containing the $gender value
         * 
         * Might want to look up 'whatever' but that only comes into play
         * if the form fails validation and you paas it back to the user
         * You could user client side javascript to replace 'whatever' with the correct value
         */
        $form->add($this->factory->createNamed('gender','choice', null, array(
            'choices'   => array($gender => 'whatever'),
            'required'  => false,
            'auto_initialize' => false,
        )));
        return;
    }
}