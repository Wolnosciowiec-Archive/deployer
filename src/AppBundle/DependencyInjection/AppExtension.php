<?php declare(strict_types=1);

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

/**
 * AppExtension
 * ============
 *
 * Registers application configuration, so all variables will be enforced to be defined
 */
class AppExtension extends Extension
{
    /**
     * @codeCoverageIgnore
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../../app/config'));
        $loader->load('services.yml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $deployer = $container->findDefinition('wolnosciowiec.api.deployer.handler');
        $deployer->addMethodCall('setConfiguration', [$config]);

        foreach ($container->findTaggedServiceIds('deployer') as $methodServiceId => $details) {
            $deployer->addMethodCall('addMethod', [new Reference($methodServiceId)]);
        }

        $container->setDefinition('wolnosciowiec.api.deployer.handler', $deployer);
    }
}
