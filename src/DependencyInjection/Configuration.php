<?php

namespace Seb\SamlBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        if (method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder('seb_saml');
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // Deprecated in Symfony 4.2
            $treeBuilder = new TreeBuilder();
            $rootNode = $treeBuilder->root('seb_saml');
        }

        $rootNode
            ->children()
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
