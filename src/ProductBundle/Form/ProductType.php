<?php

namespace ProductBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

use Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Form\Type\TypeaheadType;

use ProductBundle\Form\DataTransformer\GroupTransformer;
use ProductBundle\Form\DataTransformer\FamilyTransformer;
use ProductBundle\Form\DataTransformer\VarietyTransformer;

class ProductType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('Group', TypeaheadType::class, array(
                'compound' => false
            ))
            ->add('Family', TypeaheadType::class, array(
                'compound' => false
            ))
            ->add('Variety', TypeaheadType::class, array(
                'compound' => false
            ))
        ;

        $builder->get('Group')
            ->addModelTransformer(new GroupTransformer($this->manager));
        $builder->get('Family')
            ->addModelTransformer(new FamilyTransformer($this->manager));
        $builder->get('Variety')
            ->addModelTransformer(new VarietyTransformer($this->manager));
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ProductBundle\Entity\Product',
            'translation_domain' => 'product'
        ));
    }
}
