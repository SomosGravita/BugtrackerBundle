<?php

namespace Elemento115\BugtrackerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;

use Elemento115\BugtrackerBundle\Classes\Constants;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class BugtrackerExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!array_key_exists('registries', $config)) {
            throw new \Exception('Missing configuration option "registries". Please follow the documentation.');
        }

        // Create as many as clients as registry entries exists on the configuration file
        foreach ($config['registries'] as $name => $options) {
            $handler = new Definition('GuzzleHttp\HandlerStack');
            $handler->setFactory(['GuzzleHttp\HandlerStack', 'create']);
            $argument = [
                'base_uri' => $config[Constants::API_URL],
                'handler'  => $handler
            ];
//            // if present, add default options to the constructor argument for the Guzzle client
//            if (array_key_exists('options', $options) && is_array($options['options'])) {
//                foreach ($options['options'] as $key => $value) {
//                    if ($value === null || (is_array($value) && count($value) === 0)) {
//                        continue;
//                    }
//                    $argument[$key] = $value;
//                }
//            }
            $client = new Definition('GuzzleHttp\Client');
            $client->addArgument($argument);

            $serviceName = sprintf('%s.client.%s', 'guzzle', $name);
            $container->setDefinition($serviceName, $client);
        }
    }
}
