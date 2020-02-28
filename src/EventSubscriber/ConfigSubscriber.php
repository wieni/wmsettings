<?php

namespace Drupal\wmsettings\EventSubscriber;

use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigImporterEvent;
use Drupal\wmsettings\Service\WmSettings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    /** @var WmSettings */
    protected $wmSettings;

    public function __construct(
        WmSettings $wmSettings
    ) {
        $this->wmSettings = $wmSettings;
    }

    public static function getSubscribedEvents()
    {
        $events[ConfigEvents::IMPORT][] = ['onConfigImporterImport', 40];

        return $events;
    }

    public function onConfigImporterImport(ConfigImporterEvent $event)
    {
        $this->wmSettings->checkAndCreateEntities();
    }
}
