<?php

namespace Seb\SamlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('seb_saml');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('user_provider')->end()
                ->scalarNode('username_attribute')->defaultValue('uid')->end()
                ->scalarNode('check_path')->defaultValue('/saml/acs')->end()
                ->scalarNode('login_path')->defaultValue('/saml/login')->end()
                ->scalarNode('failure_path')->defaultValue('/login')->end()
                ->scalarNode('missing_user')->defaultValue('create')->end()
                ->booleanNode('always_use_default_target_path')->defaultValue(false)->end()
                ->variableNode('onelogin_saml_settings')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
