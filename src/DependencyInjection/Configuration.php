<?php

namespace WebEtDesign\ParameterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('web_et_design_parameter');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('types')
                ->beforeNormalization()->ifString()->then(function ($v) { return [$v]; })->end()
                ->prototype('scalar')
            ->end();

        return $treeBuilder;
    }
}
