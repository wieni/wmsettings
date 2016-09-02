<?php

namespace Drupal\wmsettings\EventSubscriber;

use Drupal\Core\Config\ConfigEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Drupal\wmsettings\Service\WmSettings;

/**
 * Event subscriber to act on config imports.
 *
 */
class ConfigSubscriber implements EventSubscriberInterface
{
    /**
     * The settings.
     */
    protected $wmSettings;

    /**
     * Constructs the ConfigSnapshotSubscriber object.
     *
     * @param \Drupal\Drupal\WmSettings\WmSettings $wm_settings
     */
    public function __construct(WmSettings $wm_settings)
    {
        $this->wmSettings = $wm_settings;
    }


    /**
    * Registers the methods in this class that should be listeners.
    *
    * @return array
    *   An array of event listener definitions.
    */
    public static function getSubscribedEvents()
    {
        $events[ConfigEvents::IMPORT][] = array('onConfigImporterImport', 40);
        return $events;
    }

    /**
     * Creates missing entities based on config.
     *
     * @param \Drupal\Core\Config\ConfigImporterEvent $event
     *   The Event to process.
     */
    public function onConfigImporterImport(ConfigImporterEvent $event)
    {
        $this->checkAndCreateEntities();
    }
}
