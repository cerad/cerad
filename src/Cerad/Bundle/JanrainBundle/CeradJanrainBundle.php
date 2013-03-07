<?php
namespace Cerad\Bundle\JanrainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Cerad\Bundle\JanrainBundle\DependencyInjection\JanrainExtension;

use Cerad\Bundle\JanrainBundle\Security\JanrainAuthenticationFactory;

class CeradJanrainBundle extends Bundle
{   
    public function getContainerExtension()
    {
        return new JanrainExtension();
    }
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new JanrainAuthenticationFactory());
    }}  
?>
