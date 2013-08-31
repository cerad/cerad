<?php
namespace Cerad\Bundle\PersonBundle\Form\Type\USSF;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/* ==================================================================
 */
class RefereeCertFormType extends AbstractType
{   
    public function getName()   { return 'cerad_person_ussf_referee_cert'; }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\PersonBundle\Entity\PersonCert'
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('badgex',    'cerad_person_ussf_referee_badge');
        $builder->add('upgrading', 'choice', array(
            'label'   => 'Working on Upgrade',
            'choices' => array('No' => 'No','Yes' => 'Yes'),
        ));
     }
}

?>
