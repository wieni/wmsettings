<?php

namespace Drupal\wmsettings\EventSubscriber;

use Drupal\Core\Config\ConfigEvents;
use Drupal\Core\Config\ConfigImporterEvent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\wmsettings\Service\WmSettings;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ConfigSubscriber implements EventSubscriberInterface
{
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var WmSettings */
    protected $wmSettings;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager,
        WmSettings $wmSettings
    ) {
        $this->entityTypeManager = $entityTypeManager;
        $this->wmSettings = $wmSettings;
    }

    public static function getSubscribedEvents()
    {
        $events[ConfigEvents::IMPORT][] = ['onConfigImporterImport', 40];

        return $events;
    }

    public function onConfigImporterImport(ConfigImporterEvent $event): void
    {
        // Don't check and create entities when a site install might be in process
        // or the settings entity type config might be importing
        if (!$this->entityTypeManager->hasDefinition('settings')) {
            return;
        }

        $this->wmSettings->checkAndCreateEntities();
    }
}
