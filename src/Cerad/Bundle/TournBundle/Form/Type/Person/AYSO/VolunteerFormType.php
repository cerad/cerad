<?php
namespace Cerad\Bundle\TournBundle\Form\Type\Person\AYSO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/* ==================================================================
 */
class VolunteerFormType extends AbstractType
{   
    public function getName()   { return 'cerad_tourn_person_ayso_volunteer'; }
    
     public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cerad\Bundle\PersonBundle\Entity\PersonLeague'
        ));
    }
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('identifier', 'cerad_person_ayso_volunteer_id');
        $builder->add('league',     'cerad_person_ayso_region_id');
     }
}

?>
