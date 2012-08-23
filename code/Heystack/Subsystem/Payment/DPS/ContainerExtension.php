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
use Heystack\Subsystem\Payment\DPS\PXFusion\Service;

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
     * {@inheritdoc}
     *
     * Adds the configuration for the payment handler.
     *
     * @param array                                                   $config
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    protected function processConfig(array $config, ContainerBuilder $container)
    {
        parent::processConfig($config, $container);

        $config = array_pop($config);

        if (isset($config['pxfusion']) && isset($config['pxfusion']['config']) && $container->hasDefinition(Services::PXFUSION_SERVICE)) {

            $definition = $container->getDefinition(Services::PXFUSION_SERVICE);

            $definition->addMethodCall('setConfig', array($config['pxfusion']['config']));

            if ($config['pxfusion']['config']['Type'] == Service::TYPE_AUTH_COMPLETE) {

                if ($container->hasDefinition(Services::PXPOST_SERVICE) && isset($config['pxpost'])) {

                    $definition->addArgument(new Reference(Services::PXPOST_SERVICE));

                } else {

                    throw new ConfigurationException('You have chosen PXFusion Auth-Complete but you haven\'t configured PXPost');

                }

            }

            if (isset($config['pxfusion']['additional_config'])) {

                $definition->addMethodCall('setAdditionalConfig', array($config['pxfusion']['additional_config']));

            }

            if ($container->hasDefinition(CoreServices::INPUT_PROCESSOR_HANDLER)) {

                $container->getDefinition(CoreServices::INPUT_PROCESSOR_HANDLER)->addMethodCall('addProcessor', array(new Reference(Services::PXFUSION_INPUT_PROCESSOR)));

            }

            if ($container->hasDefinition(CoreServices::OUTPUT_PROCESSOR_HANDLER)) {

                $container->getDefinition(CoreServices::OUTPUT_PROCESSOR_HANDLER)->addMethodCall('addProcessor', array(new Reference(Services::PXFUSION_OUTPUT_PROCESSOR)));

            }

        } else {

            throw new ConfigurationException('Please configure the pxfusion subsystem on your /mysite/config/services.yml file');

        }

        if (isset($config['pxpost']) && $container->hasDefinition(Services::PXPOST_SERVICE)) {

            $container->getDefinition(Services::PXPOST_SERVICE)->addMethodCall('setConfig', array($config['pxpost']));

            if ($container->hasDefinition(CoreServices::INPUT_PROCESSOR_HANDLER)) {

                $container->getDefinition(CoreServices::INPUT_PROCESSOR_HANDLER)->addMethodCall('addProcessor', array(new Reference(Services::PXPOST_INPUT_PROCESSOR)));

            }

            if ($container->hasDefinition(CoreServices::OUTPUT_PROCESSOR_HANDLER)) {

                $container->getDefinition(CoreServices::OUTPUT_PROCESSOR_HANDLER)->addMethodCall('addProcessor', array(new Reference(Services::PXPOST_OUTPUT_PROCESSOR)));

            }

        } else {

            throw new ConfigurationException('Please configure the pxpost subsystem on your /mysite/config/services.yml file');

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
