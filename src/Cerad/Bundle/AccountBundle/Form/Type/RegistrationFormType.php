<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',     'text',  array('label' => 'Your Name',        'attr' => array('size' => 30)))
            ->add('username', 'text',  array('label' => 'Unique User Name', 'attr' => array('size' => 30)))
            ->add('email',    'email', array('label' => 'Unique Email',     'attr' => array('size' => 30)))
                
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'Password'),
                'second_options' => array('label' => 'Confirm Password'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
        ;
    }

    public function getName()
    {
        return 'cerad_account_registration';
    }
}

?>
