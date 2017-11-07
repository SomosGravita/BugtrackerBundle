<?php

namespace Elemento115\BugtrackerBundle\DependencyInjection;

use Elemento115\BugtrackerBundle\Services\ApiClient;
use GuzzleHttp\Client;
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
            $guzzleArguments = [
                'base_uri' => $config[Constants::API_URL],
                'handler'  => $handler
            ];

            $client = new Definition(Client::class);
            $client->addArgument($guzzleArguments);

            $api = new Definition(ApiClient::class);
            $arguments = [
                'client' => $client,
                'user' => $config[Constants::API_USER],
                'password' => $config[Constants::API_PASSWORD],
                'registry' => $options[Constants::API_REGISTRY_TOKEN],
                'api_version' => $config[Constants::API_VERSION]
            ];
            $api->addArgument($arguments);

            $serviceName = sprintf('%s.client.%s', 'bugtracker', $name);
            $container->setDefinition($serviceName, $api);
        }
    }
}
