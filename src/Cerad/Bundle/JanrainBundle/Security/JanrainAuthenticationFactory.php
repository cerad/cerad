<?php
namespace Cerad\Bundle\JanrainBundle\Security;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\DefinitionDecorator;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\AbstractFactory;

class JanrainAuthenticationFactory extends AbstractFactory
{
    public function getPosition() { return 'http';  }
    public function getKey()      { return 'cerad_janrain'; } // Matches firewall
    
    public function isRememberMeAware($config) { return false; }
    
    public function __construct()
    {
        $this->addOption('rpx_api_key',   null);
        $this->addOption('add_path',      null);
        $this->addOption('register_path', null);
    }
    protected function getListenerId()
    {
        return 'cerad_janrain.security.authentication.listener';
    }
    protected function createAuthProvider(ContainerBuilder $container, $firewallKey, $config, $userProviderId)
    {
        $authProviderId = 'cerad_janrain.security.authentication.provider.' . $firewallKey;
        $container
            ->setDefinition($authProviderId, new DefinitionDecorator('cerad_janrain.security.authentication.provider'))
            ->replaceArgument(0, new Reference($userProviderId))
            ->replaceArgument(1, $firewallKey)
        ;
        return $authProviderId;
    }
    protected function createEntryPoint($container, $firewallKey, $config, $defaultEntryPoint)
    {
        $entryPointId = 'cerad_janrain.security.authentication.entry_point.' . $firewallKey;

        $container
            ->setDefinition($entryPointId, new DefinitionDecorator('cerad_janrain.security.authentication.entry_point'))
            ->addArgument($config['login_path'])
        ;

        return $entryPointId;
    }
}
?>
