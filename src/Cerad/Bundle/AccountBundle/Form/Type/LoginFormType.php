<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoginFormType extends AbstractType
{
   public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'intention'       => 'authenticate', // Needs to match security.yml
        ));
    }
    public function getName() { return 'cerad_account_login'; }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',   'cerad_account_username_existing')
            ->add('password',   'password',  array('label' => 'Zayso Password',  'attr' => array('size' => 30)))
            ->add('remember_me','checkbox',  array('label' => 'Remember Me'))
        ;
    }
}

?>
