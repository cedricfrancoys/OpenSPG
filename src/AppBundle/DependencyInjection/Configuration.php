<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const DEFAULT_JS_FILE = '%kernel.root_dir%/../src/AppBundle/Resources/public/js/typeahead.jquery.js';
    const DEFAULT_JS_OUTPUT = 'js/typeahead.js';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('twitter_typeahead');
        $rootNode
            ->children()
                ->arrayNode('auto_configure')
                    ->info('Automatically configure subsystems?')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('assetic')->defaultTrue()->end()
                        ->booleanNode('twig')->defaultTrue()->end()
                    ->end()
                ->end()
                ->scalarNode('typeahead_js_file')
                    ->defaultValue(self::DEFAULT_JS_FILE)
                    ->info('(for assetic) Location of the typeaheadbundle.js file (normally the file that comes with twitter/typeahead.js')
                ->end()
                ->scalarNode('typeahead_js_output')
                    ->defaultValue(self::DEFAULT_JS_OUTPUT)
                    ->info('(for assetic) Output location for typeahead JS code. Should be relative to your web directory.')
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
