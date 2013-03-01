<?php
namespace Cerad\Bundle\JanrainBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\JanrainBundle\DependencyInjection\JanrainExtension;

class CeradJanrainBundle extends Bundle
{   
    public function getContainerExtension()
    {
        return new JanrainExtension();
    }
}  
?>
