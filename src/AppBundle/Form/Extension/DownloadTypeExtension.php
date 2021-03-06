<?php
namespace AppBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DownloadTypeExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return string The name of the type being extended
     */
    public function getExtendedType()
    {
        return FileType::class;
    }

    /**
     * Add the image_path option
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array(
        	'file_path'
        ));
    }

    /**
     * Pass the image URL to the view
     *
     * @param FormView $view
     * @param FormInterface $form
     * @param array $options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (array_key_exists('file_path', $options)) {
            $parentData = $form->getParent()->getData();

            if (null !== $parentData) {
                $accessor = PropertyAccess::createPropertyAccessor();
                $fileUrl = $accessor->getValue($parentData, $options['file_path']);
                
                if(!file_exists($this->getWebRoot().$fileUrl) || !is_file($this->getWebRoot().$fileUrl)){
                	$fileUrl = null;
                }
            } else {
                 $fileUrl = null;
            }

            // set an "image_url" variable that will be available when rendering this field
            $view->vars['file_url'] = $fileUrl;
            if ($view->vars['file_url'] !== null) {
                $view->vars['file_name'] = explode('/', $view->vars['file_url']);
                $view->vars['file_name'] = $view->vars['file_name'][count($view->vars['file_name'])-1];
            }else{
                $view->vars['file_name'] = null;
            }
        }
    }

    protected function getWebRoot()
    {
    	return __DIR__.'/../../../../web/';
    }
}