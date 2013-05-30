<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PasswordResetFormType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username','cerad_account_username_existing')
        ;
    }
    public function getName()
    {
        return 'cerad_account_password_reset';
    }
}

?>
