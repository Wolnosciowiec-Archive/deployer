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
        $rootNode = $treeBuilder->root('heroku_deploy');

        $rootNode
            ->children()
            ->arrayNode('repositories')
                ->prototype('array')
                    ->children()
                        ->scalarNode('git_url')->isRequired(true)->end()
                        ->scalarNode('git_branch')->isRequired(true)->end()
                        ->enumNode('git_branch_math')->values(['exact', 'regexp'])->isRequired(true)->end()
                        ->scalarNode('heroku_login')->isRequired(true)->end()
                        ->scalarNode('heroku_password')->isRequired(true)->end()
                        ->scalarNode('heroku_name')->isRequired(true)->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}