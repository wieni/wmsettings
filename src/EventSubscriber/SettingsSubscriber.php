<?php

namespace Drupal\wmsettings\EventSubscriber;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\hook_event_dispatcher\Event\Entity\BaseEntityEvent;
use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SettingsSubscriber implements EventSubscriberInterface
{
    /** @var CacheTagsInvalidatorInterface */
    protected $tagsInvalidator;

    public function __construct(
        CacheTagsInvalidatorInterface $tagsInvalidator
    ) {
        $this->tagsInvalidator = $tagsInvalidator;
    }

    public static function getSubscribedEvents()
    {
        return [
            HookEventDispatcherInterface::ENTITY_INSERT => 'dispatchCacheTags',
            HookEventDispatcherInterface::ENTITY_UPDATE => 'dispatchCacheTags',
        ];
    }

    /**
     * Invalidate the ads api endpoint when saving the ads settings.
     */
    public function dispatchCacheTags(BaseEntityEvent $event)
    {
        $entity = $event->getEntity();

        if (
            !$entity instanceof FieldableEntityInterface
            || !$entity->hasField('wmsettings_key')
        ) {
            return;
        }

        $this->tagsInvalidator->invalidateTags([
            sprintf('wmsettings:%s', $entity->get('wmsettings_key')->value)
        ]);
    }
}