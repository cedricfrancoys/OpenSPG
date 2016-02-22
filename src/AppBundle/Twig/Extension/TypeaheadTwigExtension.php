<?php

namespace AppBundle\Twig\Extension;

use Assetic\Asset\AssetCollection;
use Assetic\Asset\StringAsset;
use Assetic\Filter\Yui\JsCompressorFilter;
use AppBundle\Form\Type\TypeaheadType;

class TypeaheadTwigExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'twitter_typeahead';
    }

    public function getFunctions()
    {
        return array(
            'twitter_typeahead_init' => new \Twig_Function_Method($this, 'initTypeaheadFunction', array('needs_environment' => true, 'is_safe' => array('html'))),
        );
    }

    public function initTypeaheadFunction(\Twig_Environment $env)
    {
        if (!TypeaheadType::$initialized) {
            TypeaheadType::$initialized = true;
            return '';
            // $url = $env->getExtension('assets')->getAssetUrl('bundles/lifotypeahead/js/typeaheadbundle.js');
            // return "<script type=\"text/javascript\" src=\"$url\"></script>";
        }
        return '';
    }

}
