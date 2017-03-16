<?php

namespace AppBundle\Form\Type;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManager;
use AppBundle\Form\DataTransformer\EntityToPropertyTransformer;
use AppBundle\Form\DataTransformer\EntitiesToPropertyTransformer;

class TypeaheadType extends AbstractType
{
    public static $initialized = false;

    protected $container;
    protected $em;
    protected $router;

    public function __construct(Container $container, EntityManager $em, Router $router)
    {
        $this->container = $container;
        $this->em = $em;
        $this->router = $router;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {}

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);

        $view->vars['url'] = $options['url'];
        $view->vars['dependency'] = $options['dependency'];
    }

    public function configureOptions(OptionsResolver $resolver) // sf2.6+
    {
        $resolver->setDefaults(array(
            'dependency' => false
        ));
        $resolver->setRequired(array('url'));
    }

    public function getName()
    {
        return 'entity_typeahead';
    }

    public function getBlockPrefix()
    {
        return 'entity_typeahead';
    }
}
