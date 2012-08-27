<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Payment
 */

/**
 * Payment namespace
 */
namespace Heystack\Subsystem\Payment\DPS;

use Heystack\Subsystem\Core\Services as CoreServices;

use Heystack\Subsystem\Core\ContainerExtensionConfigProcessor;
use Heystack\Subsystem\Core\Exception\ConfigurationException;
use Heystack\Subsystem\Payment\DPS\PXFusion\Service as PXFusionService;

use Symfony\Component\Config\FileLocator;

use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 *
 * @copyright  Heyday
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @package Ecommerce-Payment
 *
 */
class ContainerExtension extends ContainerExtensionConfigProcessor implements ExtensionInterface
{

    /**
     * Loads a services.yml file into a fresh container, ready to me merged
     * back into the main container
     *
     * @param  array            $config
     * @param  ContainerBuilder $container
     * @return null
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(ECOMMERCE_PAYMENT_BASE_PATH . '/config')
        );

        $loader->load('dps_services.yml');

        $this->processConfig($config, $container);
    }

    /**
     * @param array $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @throws \Heystack\Subsystem\Core\Exception\ConfigurationException
     */
    protected function processConfig(array $config, ContainerBuilder $container)
    {
        parent::processConfig($config, $container);

        $config = array_pop($config);

        $dataObjectGenerator =
            $container->hasDefinition(CoreServices::DATAOBJECT_GENERATOR)
            ? $container->getDefinition(CoreServices::DATAOBJECT_GENERATOR)
            : false;

        $outputProcessorHandler =
            $container->hasDefinition(CoreServices::OUTPUT_PROCESSOR_HANDLER)
                ? $container->getDefinition(CoreServices::OUTPUT_PROCESSOR_HANDLER)
                : false;

        $inputProcessorHandler =
            $container->hasDefinition(CoreServices::INPUT_PROCESSOR_HANDLER)
                ? $container->getDefinition(CoreServices::INPUT_PROCESSOR_HANDLER)
                : false;

        $ssOrm =
            $container->hasDefinition(CoreServices::SS_ORM_BACKEND)
                ? $container->getDefinition(CoreServices::SS_ORM_BACKEND)
                : false;

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

            if ($inputProcessorHandler) {
                $inputProcessorHandler->addMethodCall(
                    'addProcessor',
                    array(
                        new Reference(Services::PXFUSION_INPUT_PROCESSOR)
                    )
                );
            }

            if ($outputProcessorHandler) {
                $outputProcessorHandler->addMethodCall(
                    'addProcessor',
                    array(
                        new Reference(Services::PXFUSION_OUTPUT_PROCESSOR)
                    )
                );
            }

            if ($dataObjectGenerator) {
                $dataObjectGenerator->addMethodCall(
                    'addYamlSchema',
                    array(
                        'ecommerce-payment/config/storage/pxfusionpayment.yml'
                    )
                );

                $dataObjectGenerator->addMethodCall(
                    'addYamlSchema',
                    array(
                        'ecommerce-payment/config/storage/transaction_pxfusionpayment.yml',
                    )
                );
            }

            if ($ssOrm) {
                $ssOrm->addMethodCall(
                    'addDataProvider',
                    array(
                        new Reference(Services::PXFUSION_PAYMENT_RESPONSE)
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

            if ($inputProcessorHandler) {
                $inputProcessorHandler->addMethodCall(
                    'addProcessor',
                    array(
                        new Reference(Services::PXPOST_INPUT_PROCESSOR)
                    )
                );
            }

            if ($outputProcessorHandler) {
                $outputProcessorHandler->addMethodCall(
                    'addProcessor',
                    array(
                        new Reference(Services::PXPOST_OUTPUT_PROCESSOR)
                    )
                );
            }

            if ($dataObjectGenerator) {
                $dataObjectGenerator->addMethodCall(
                    'addYamlSchema',
                    array(
                        'ecommerce-payment/config/storage/pxpostpayment.yml'
                    )
                );

                $dataObjectGenerator->addMethodCall(
                    'addYamlSchema',
                    array(
                        'ecommerce-payment/config/storage/transaction_pxpostpayment.yml'
                    )
                );
            }

            if ($ssOrm) {
                $ssOrm->addMethodCall(
                    'addDataProvider',
                    array(
                        new Reference(Services::PXPOST_PAYMENT_RESPONSE)
                    )
                );
            }


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
