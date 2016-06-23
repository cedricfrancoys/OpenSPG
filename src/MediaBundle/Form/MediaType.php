<?php

namespace MediaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Persistence\ObjectManager;

use MediaBundle\Form\DataTransformer\ParentToNumberTransformer;

use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class MediaType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('title', null, array(
                'required' => true
            ))
            ->add('mediaFile', VichImageType::class, array(
                'required' => true
            ))
            ->add('parent', HiddenType::class, array(
                'required' => false
            ))
        ;

        $builder->get('parent')
            ->addModelTransformer(new ParentToNumberTransformer($this->manager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MediaBundle\Entity\Media',
            'translation_domain' => 'media'
        ));
    }
}