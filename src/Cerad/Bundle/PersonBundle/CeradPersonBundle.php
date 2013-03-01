<?php
namespace Cerad\Bundle\PersonBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\PersonBundle\DependencyInjection\PersonExtension;

class CeradPersonBundle extends Bundle
{  
    public function getContainerExtension()
    {
        return new PersonExtension();
    }
}   
?>
