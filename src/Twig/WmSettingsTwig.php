<?php

namespace Drupal\wmsettings\Twig;

use Drupal\wmsettings\Service\WmSettings;

/**
 * Class WmSettingsTwig
 * @package Drupal\wmsettings\Twig
 */
class WmSettingsTwig extends \Twig_Extension
{
    /** @var WmSettings */
    protected $wmSettings;

    /**
     * WmSettingsTwig constructor.
     *
     * @param \Drupal\wmsettings\Service\WmSettings $wmSettings
     */
    public function __construct(WmSettings $wmSettings)
    {
        $this->wmSettings = $wmSettings;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'wmsettings.twig_extension.wmsettings';
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('wmsettings', array($this, 'wmsettings'), array(
                'is_safe' => array('html'),
                'needs_environment' => false,
                'needs_context' => false,
                'is_variadic' => false,
            )),
        );
    }

    /**
     * Hopefully returning the model of the settings bundle.
     * @param $settings
     *
     * @return mixed
     */
    public function wmsettings($settings)
    {
        return $this->wmSettings->read($settings);
    }
}
