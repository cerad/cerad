<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Person\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/* ==================================================================
 */
class RefereeFormType extends AbstractType
{   
    public function getName()   { return 'cerad_tourn_person_ayso_referee'; }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\PersonBundle\Entity\PersonCert'
        ));
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('badgex', 'cerad_person_ayso_referee_badge');
     }
}

?>
