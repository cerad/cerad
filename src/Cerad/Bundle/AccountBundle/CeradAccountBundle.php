<?php
namespace Cerad\Bundle\AccountBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\AccountBundle\DependencyInjection\AccountExtension;

class CeradAccountBundle extends Bundle
{  
    public function getContainerExtension()
    {
        return new AccountExtension();
    }
}   
?>
