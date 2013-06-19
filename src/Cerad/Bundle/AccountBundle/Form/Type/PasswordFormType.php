<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Validator\Constraints as Assert;

class PasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
      // $builder->addModelTransformer(new VolunteerIDTransformer());
    }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'type'            => 'password',
            'label'           => 'User Password',
            'attr'            => array('size' => 20),
            'required'        => false,
            
            'invalid_message' => 'Passwords must match.',
                
            'first_options'  => array('label' => 'User Password'),
            'second_options' => array('label' => 'User Password(confirm)'),
            
            'first_name'  => 'pass1',
            'second_name' => 'pass2', // form.userPass.pass1
        ));
        /*
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
        ));*/

    }
    public function getParent() { return 'repeated'; }
    public function getName()   { return 'cerad_account_password'; }
}

?>
