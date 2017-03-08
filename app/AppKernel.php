<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            new Bazinga\Bundle\JsTranslationBundle\BazingaJsTranslationBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            
            new Sonata\CoreBundle\SonataCoreBundle(),
            new Sonata\BlockBundle\SonataBlockBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
            new Sonata\AdminBundle\SonataAdminBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),

            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new WhiteOctober\BreadcrumbsBundle\WhiteOctoberBreadcrumbsBundle(),

            new AppBundle\AppBundle(),
            new UserBundle\UserBundle(),
            new ProductBundle\ProductBundle(),
            new ProducerBundle\ProducerBundle(),
            // new MemberBundle\MemberBundle(),
            new ConsumerBundle\ConsumerBundle(),
            new NodeBundle\NodeBundle(),
            new LocationBundle\LocationBundle(),
            new CartBundle\CartBundle(),

            new ManagementBundle\ManagementBundle(),
            new FeeBundle\FeeBundle(),
            new PageBundle\PageBundle(),
            new NewsBundle\NewsBundle(),
        ];

        if (in_array($this->getEnvironment(), ['dev', 'test'], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
            $bundles[] = new Liip\FunctionalTestBundle\LiipFunctionalTestBundle();
        }

        return $bundles;
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__).'/var/cache/'.$this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__).'/var/logs';
    }

    public static function fromEnvironment()
    {
        $env = getenv('APP_ENVIRONMENT') ?: 'prod';
        $debug = filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN);
        return new self($env, $debug);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }
}
