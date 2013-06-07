<?php
namespace Cerad\Bundle\TournAdminBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

use Cerad\Bundle\TournAdminBundle\DependencyInjection\TournAdminExtension;

class CeradTournAdminBundle extends Bundle
{  
    public function getContainerExtension()
    {
        return new TournAdminExtension();
    }
}   
?>
