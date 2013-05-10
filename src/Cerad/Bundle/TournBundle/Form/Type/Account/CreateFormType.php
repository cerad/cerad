<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Account;

//use Zayso\CoreBundle\Constraint\UserNameConstraint;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Validator\Constraints\NotBlank;

class CreateFormType extends AbstractType
{
    protected $manager = null;
    
    public function getName() { return 'cerad_tourn_account_create'; }
    
    public function __construct($manager)
    {
        $this->manager = $manager;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $notBlank = new NotBlank();
        
        $builder->add('userName', 'text',  array(
            'label' => 'Zayso User Name', 
            'attr' => array('size' => 40),
      //    'constraints' => new UserNameConstraint(),
        ));
        
        $builder->add('userPass', 'repeated', array(
            'type'     => 'password',
            'label'    => 'Zayso Password',
            'required' => true,
            'invalid_message' => 'The password fields must match.',
            'constraints' => $notBlank,
            
            'first_options'  => array('label' => 'Zayso Password'),
            'second_options' => array('label' => 'Zayso Password(confirm)'),
            
            'first_name'  => 'pass1',
            'second_name' => 'pass2', // form.userPass.pass1
        ));
        $builder->add('firstName', 'text', array('label' => 'AYSO First Name', 'constraints' => $notBlank));
        $builder->add('lastName',  'text', array('label' => 'AYSO Last Name',  'constraints' => $notBlank));
        $builder->add('nickName',  'text', array('label' => 'Nick Name',       'required' => false,));

        $builder->add('email', 'email', array('label' => 'Email','attr' => array('size' => 35)));
        
        $builder->add('phone', 'text',  array('label' => 'Cell Phone', 'attr' => array('size' => 20), 'required' => false,));
        
        // AYSO Stuff
        $builder->add('aysoid', 'cerad_person_ayso_volunteer_id',  array('label' => 'AYSO Vol ID'));
        $builder->add('region', 'cerad_person_ayso_region_id',     array('label' => 'AYSO Region Number'));
        $builder->add('badge',  'cerad_person_ayso_referee_badge', array('label' => 'AYSO Referee Badge'));
    }
}