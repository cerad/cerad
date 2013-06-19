<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EditFormType extends AbstractType
{
    public function getName() { return 'cerad_account_create'; }
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class'        => 'Cerad\Bundle\AccountBundle\Entity\AccountUser',
            'intention'         => 'create',
            'validation_groups' => array('update'),
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
          //->add('name',          'text')
            ->add('name',          'cerad_account_displayname')
            ->add('username',      'cerad_account_username')
            ->add('email',         'cerad_account_email')    
            ->add('plainPassword', 'cerad_account_password')
        ;
    }
}

?>
