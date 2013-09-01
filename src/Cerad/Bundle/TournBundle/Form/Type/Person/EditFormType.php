<?php
/* =========================================
 * For a given tournament we want to control what the people can see
 * Eventually this should be part of the project setup file and created dynamically
 * 
 * Or possible override with a form class if $builder->remove is supported
 */
namespace Cerad\Bundle\TournBundle\Form\Type\Person;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditFormType extends AbstractType
{
    public function getName()   { return 'cerad_tourn_person_edit'; }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'         => 'Cerad\Bundle\PersonBundle\Entity\Person',
            'validation_groups'  => array('update'),
            'cascade_validation' => true,
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name',      'text', array('label' => 'Full Name'));
        $builder->add('firstName', 'text', array('label' => 'AYSO First Name'));
        $builder->add('lastName',  'text', array('label' => 'AYSO Last Name' ));
        $builder->add('nickName',  'text', array('label' => 'Nick Name','required' => false));
        
        $builder->add('phone', 'cerad_person_phone',  array('required' => false));
        $builder->add('email', 'cerad_person_email',  array('required' => true));
    }
}
?>
