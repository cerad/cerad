<?php
namespace Cerad\Bundle\ArbiterBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\ArbiterBundle\DependencyInjection\ArbiterExtension;

class CeradArbiterBundle extends Bundle
{  
    public function getContainerExtension()
    {
        return new ArbiterExtension();
    }
}   
?>
