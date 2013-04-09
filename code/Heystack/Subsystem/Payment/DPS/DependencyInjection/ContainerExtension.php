<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Payment
 */

/**
 * Payment namespace
 */
namespace Heystack\Subsystem\Payment\DPS\DependencyInjection;

use Heystack\Subsystem\Core\Exception\ConfigurationException;
use Heystack\Subsystem\Payment\DPS\Config\ContainerConfig;
use Heystack\Subsystem\Payment\DPS\PXFusion\Service as PXFusionService;
use Heystack\Subsystem\Payment\DPS\Services;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 *
 * @copyright  Heyday
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Payment
 *
 */
class ContainerExtension extends Extension
{

    /**
     * Loads a services.yml file into a fresh container, ready to me merged
     * back into the main container
     *
     * @param  array            $configs
     * @param  ContainerBuilder $container
     * @return null
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(ECOMMERCE_PAYMENT_BASE_PATH . '/config')
        );

        $loader->load('dps_services.yml');

        $this->processConfig(
            (new Processor())->processConfiguration(
                new ContainerConfig(),
                $configs
            ),
            $container
        );
    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @throws \Heystack\Subsystem\Core\Exception\ConfigurationException
     */
    protected function processConfig(array $config, ContainerBuilder $container)
    {
        if (
            isset($config['pxfusion'])
            && isset($config['pxfusion']['config'])
            && $container->hasDefinition(Services::PXFUSION_SERVICE)
        ) {

            $pxfusionService = $container->getDefinition(Services::PXFUSION_SERVICE);

            $pxfusionService->addMethodCall(
                'setConfig',
                array(
                    $config['pxfusion']['config'],
                    true
                )
            );

            if (isset($config['pxfusion']['config']['Type']) && $config['pxfusion']['config']['Type'] == PXFusionService::TYPE_AUTH_COMPLETE) {

                if ($container->hasDefinition(Services::PXPOST_SERVICE) && isset($config['pxpost'])) {

                    $pxfusionService->addArgument(new Reference(Services::PXPOST_SERVICE));

                } else {

                    throw new ConfigurationException(
                        'You have chosen PXFusion Auth-Complete but you haven\'t configured PXPost'
                    );

                }

            }

            if (isset($config['pxfusion']['additional_config'])) {
                $pxfusionService->addMethodCall(
                    'setAdditionalConfig',
                    array(
                        $config['pxfusion']['additional_config'],
                        true
                    )
                );
            }

        }

        if (
            isset($config['pxpost'])
            && isset($config['pxpost']['config'])
            && $container->hasDefinition(Services::PXPOST_SERVICE)
        ) {
            $pxpostService = $container->getDefinition(Services::PXPOST_SERVICE);
            $pxpostService->addMethodCall(
                'setConfig',
                array(
                    $config['pxpost']['config'],
                    true
                )
            );

            if (isset($config['pxpost']['additional_config'])) {
                $pxpostService->addMethodCall(
                    'setAdditionalConfig',
                    array(
                        $config['pxpost']['additional_config'],
                        true
                    )
                );
            }            
        }
        
        if(isset($config['yml.transaction_pxfusion_payment']) && $container->hasDefinition('transaction_pxfusion_payment_schema')){
            
            $definition = $container->getDefinition('transaction_pxfusion_payment_schema');
            
            $definition->replaceArgument(0, $config['yml.transaction_pxfusion_payment']);
            
        }
        
        if(isset($config['yml.pxfusion_payment']) && $container->hasDefinition('pxfusion_payment_schema')){
            
            $definition = $container->getDefinition('pxfusion_payment_schema');
            
            $definition->replaceArgument(0, $config['yml.pxfusion_payment']);
            
        }
        
        if(isset($config['yml.pxpost_payment']) && $container->hasDefinition('pxpost_payment_schema')){
            
            $definition = $container->getDefinition('pxpost_payment_schema');
            
            $definition->replaceArgument(0, $config['yml.pxpost_payment']);
            
        }
        
        if(isset($config['yml.transaction_pxpost_payment']) && $container->hasDefinition('transaction_pxpost_payment_schema')){
            
            $definition = $container->getDefinition('transaction_pxpost_payment_schema');
            
            $definition->replaceArgument(0, $config['yml.transaction_pxpost_payment']);
            
        }

    }

    /**
     * Returns the namespace of the container extension
     * @return type
     */
    public function getNamespace()
    {
        return 'dps';
    }

    /**
     * Returns Xsd Validation Base Path, which is not used, so false
     * @return boolean
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * Returns the container extensions alias
     * @return type
     */
    public function getAlias()
    {
        return 'dps';
    }

}
