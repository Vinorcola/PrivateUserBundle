<?php

namespace Vinorcola\PrivateUserBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('vinorcola_private_user');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->fixXmlConfig('type')
            ->children()
                ->scalarNode('sending_email_address')
                    ->isRequired()
                ->end() //sending_email_address
                ->scalarNode('default_type')
                    ->defaultNull()
                    ->validate()
                        ->ifTrue(function ($value) {
                            return $value !== null && !is_string($value);
                        })
                        ->thenInvalid('Default type must be a string or null.')
                    ->end()
                ->end() // default_type
                ->arrayNode('types')
                    ->defaultValue([
                        'user' => [
                            'roles' => [ 'ROLE_USER' ],
                        ],
                    ])
                    ->requiresAtLeastOneElement()
                    ->normalizeKeys(false)
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->fixXmlConfig('role')
                        ->children()
                            ->arrayNode('roles')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->scalarPrototype()
                                    ->validate()
                                        ->ifTrue(function ($value) {
                                            return !is_string($value) || substr($value, 0, 5) !== 'ROLE_';
                                        })
                                        ->thenInvalid('Role must start by "ROLE_".')
                                    ->end()
                                ->end()
                            ->end() // roles
                        ->end()
                    ->end()
                ->end() // types
            ->end();

        return $treeBuilder;
    }
}
