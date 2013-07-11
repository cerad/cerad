<?php
namespace Cerad\Bundle\AccountBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
//  Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TokenFormType extends AbstractType
{
    public function getName()   { return 'cerad_account_token'; }
    public function getParent() { return 'text'; }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'type'            => 'text',
            'label'           => 'Confirmation # From Email',
            'attr'            => array('size' => 20),
            'required'        => true,
        ));
    }
}

?>
