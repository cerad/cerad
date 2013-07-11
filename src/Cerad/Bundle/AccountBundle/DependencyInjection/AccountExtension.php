<?php

namespace Cerad\Bundle\AccountBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Config\FileLocator;

class AccountExtension extends Extension
{
    public function getAlias() { return 'cerad_account'; }
        
    public function load(array $configs, ContainerBuilder $container)
    {
        // Simple merge for possible multiple source (dev etc)
        $config = array();
        foreach ($configs as $subConfig) 
        {
            $config = array_merge($config, $subConfig);
        }
        // For now just store as a parameter
        $container->setParameter('cerad_account.config',$config);
        
        $names = array
        (   
            'firewall_name',
            'user_class',
            'user_identifier_class',
        );
        foreach($names as $name)
        {
            $value = isset($config[$name]) ? $config[$name] : null;
            
            $container->setParameter('cerad_account.' . $name,$value); 
        }
        // Load the services
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
       
        // real dangerous, change the user provider service
        if (isset($config['service']['person_manager']))
        {
            $personManagerId = $config['service']['person_manager'];
            $definition = $container->getDefinition('cerad_account.user_provider');
            $definition->addArgument(new Reference($personManagerId));
        }
    }
}
