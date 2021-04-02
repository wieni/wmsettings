<?php

namespace Drupal\wmsettings\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmsettings\Service\WmSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SettingsMenuItemsDeriver extends DeriverBase implements ContainerDeriverInterface
{
    use StringTranslationTrait;

    /** @var WmSettings */
    protected $settings;

    public static function create(ContainerInterface $container, $basePluginId)
    {
        $instance = new static();
        $instance->settings = $container->get('wmsettings.settings');

        return $instance;
    }

    public function getDerivativeDefinitions($base_plugin_definition)
    {
        foreach ($this->settings->readKeys() ?? [] as $key => $config) {
            if (!$entity = $this->settings->read($key)) {
                continue;
            }

            $editUrl = $entity->toUrl('edit-form');

            $this->derivatives[sprintf('wmsettings.%s', $entity->bundle())] = [
                'title' => $config['label'],
                'route_name' => $editUrl->getRouteName(),
                'route_parameters' => $editUrl->getRouteParameters(),
            ] + $base_plugin_definition;
        }

        return parent::getDerivativeDefinitions($base_plugin_definition);
    }
}
