<?php

namespace MediaBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Doctrine\Common\Persistence\ObjectManager;

use AppBundle\Form\DataTransformer\EntityToIdTransformer;
use MediaBundle\Entity\Media;

/**
 * File type for Media Files
 */
class FileType extends AbstractType
{
    private $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new EntityToIdTransformer(
            $this->manager,
            'MediaBundle:Media',
            'id',
            null,
            false
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'multiple' => false,
            'changeDir' => false,
            'delete' => false,
            'parent' => null,
            'file' => null
        ));

        $resolver->setRequired(array(
            'uploadForm'
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // dump($options);
        if (is_string($options['parent']) && !is_numeric($options['parent'])) {
            $options['parent'] = $this->getIdFromString($options['parent']);
        }

        $view->vars = array_replace($view->vars, $options);

        // dump($view->vars);
        if ($view->vars['value']) {
            $repo = $this->manager->getRepository('MediaBundle:Media');
            $media = $repo->find($view->vars['value']);
            // $directory = $repo->getPathArray($media);
            $view->vars['file'] = $media->getSlug();
            $view->vars['filename'] = $media->getFilename();
        }
    }

    public function getBlockPrefix()
    {
        return 'media_file';
    }

    protected function getIdFromString($string)
    {
        $dirs = explode('/', $string);
        dump($dirs);
        $repo = $this->manager->getRepository('MediaBundle:Media');
        $parent = null;
        foreach ($dirs as $dir) {
            $media = $repo->findOneBy(array(
                'type' => 'Directory',
                'title' => $dir,
                'parent' => $parent
            ));
            if (!$media) {
                $media = new Media();
                $media->setType('Directory');
                $media->setMime('inode/directory');
                $media->setTitle($dir);
                $media->setMedia($dir);
                $media->setFilename($dir);
                if (!$parent) {
                    $repo->persistAsFirstChild($media);
                }else{
                    $repo->persistAsFirstChildOf($media, $parent);
                }
                $this->manager->flush();
            }
            $parent = $media;
        }
        return $media->getId();
    }
}
