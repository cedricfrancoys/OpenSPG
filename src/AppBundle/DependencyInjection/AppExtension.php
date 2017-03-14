<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

class AppExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        //        $configuration = new Configuration();
//        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $configs = $container->getExtensionConfig($this->getAlias());
        $config = $this->processConfiguration(new Configuration(), $configs);

        // "twitter_typeahead: auto_configure: assetic: true"
        if (isset($bundles['AsseticBundle']) && $config['auto_configure']['assetic']) {
            $this->configureAsseticBundle($container, $config);
        }

        // "twitter_typeahead: auto_configure: twig: true"
        if (isset($bundles['TwigBundle']) && $config['auto_configure']['twig']) {
            $this->configureTwigBundle($container, $config);
        }
    }

    /**
     * @param ContainerBuilder $container The service container
     * @param array            $config    The bundle configuration
     */
    protected function configureAsseticBundle(ContainerBuilder $container, array $config)
    {
        if ($container->hasExtension('assetic')) {
            $container->prependExtensionConfig('assetic', array(
                'assets' => array(
                    // apply in twig template via a javascripts tag using "@twitter_typeahead_js"
                    'twitter_typeahead_js' => array(
                        'inputs' => array(
                            $config['typeahead_js_file'],
                        ),
                        'output' => $config['typeahead_js_output'],
                    ),
                ),
            ));
        }
    }

    /**
     * @param ContainerBuilder $container The service container
     * @param array            $config    The bundle configuration
     */
    protected function configureTwigBundle(ContainerBuilder $container, array $config)
    {
        if ($container->hasExtension('twig')) {
            $resources = array('AppBundle:Form:typeahead.html.twig');
            if (Kernel::VERSION_ID >= '20600') {
                $container->prependExtensionConfig('twig', array('form_themes' => $resources));
            } else {
                $container->prependExtensionConfig('twig', array('form' => array('resources' => $resources)));
            }
        }
    }
}
