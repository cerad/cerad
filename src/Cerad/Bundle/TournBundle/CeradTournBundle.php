<?php
namespace Cerad\Bundle\TournBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\TournBundle\DependencyInjection\TournExtension;

class CeradTournBundle extends Bundle
{  
    public function getContainerExtension()
    {
        return new TournExtension();
    }
}   
?>
