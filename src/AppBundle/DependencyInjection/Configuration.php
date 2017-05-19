<?php declare(strict_types=1);

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('deploy');

        $rootNode
            ->children()
            ->arrayNode('repositories')
                ->prototype('array')
                    ->children()
                        ->scalarNode('git_url')->isRequired(true)->end()
                        ->scalarNode('git_branch')->isRequired(true)->end()
                        ->enumNode('git_branch_math')->values(['exact', 'regexp'])->isRequired(true)->end()

                        ->arrayNode('heroku')
                            ->children()
                                ->scalarNode('login')->isRequired(true)->end()
                                ->scalarNode('token')->isRequired(true)->end()
                                ->scalarNode('name')->isRequired(true)->end()
                            ->end()
                        ->end()

                        ->arrayNode('thin_deployer')
                            ->children()
                                ->scalarNode('url')->isRequired(true)->end()
                                ->scalarNode('service_name')->isRequired(true)->end()
                                ->scalarNode('token')->isRequired(true)->end()
                            ->end()
                        ->end()
                    ->end()

                ->end()
            ->end();

        return $treeBuilder;
    }
}