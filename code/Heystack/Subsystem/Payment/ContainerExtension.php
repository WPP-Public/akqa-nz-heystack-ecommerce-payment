<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Payment
 */

/**
 * Payment namespace
 */
namespace Heystack\Subsystem\Payment;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Heystack\Subsystem\Core\ContainerExtensionConfigProcessor;

use Heystack\Subsystem\Core\ConfigurationException;

/**
 * Container extension for Heystack.
 *
 * If Heystacks services are loaded as an extension (this happens when there is
 * a primary services.yml file in mysite/config) then this is the container
 * extension that loads heystacks services.yml
 *
 * @copyright  Heyday
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
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

        $loader->load('services.yml');

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

        if (isset($config['config']) &&  $container->hasDefinition(Services::PAYMENT_HANDLER)) {

           $container->getDefinition(Services::PAYMENT_HANDLER)->addMethodCall('setConfig', array($config['config']));

        } else {
            throw new ConfigurationException('Please configure the payment subsystem on your /mysite/config/services.yml file');
        }
    }

    /**
     * Returns the namespace of the container extension
     * @return type
     */
    public function getNamespace()
    {
        return 'payment';
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
        return 'payment';
    }

}
