<?php

namespace Drupal\wmsettings\EventSubscriber;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;

class SettingsSubscriber
{
    /** @var CacheTagsInvalidatorInterface */
    protected $tagsInvalidator;

    public function __construct(
        CacheTagsInvalidatorInterface $tagsInvalidator
    ) {
        $this->tagsInvalidator = $tagsInvalidator;
    }

    public function dispatchCacheTags(EntityInterface $entity)
    {
        if (
            !$entity instanceof FieldableEntityInterface
            || !$entity->hasField('wmsettings_key')
        ) {
            return;
        }

        $this->tagsInvalidator->invalidateTags([
            sprintf('wmsettings:%s', $entity->get('wmsettings_key')->value),
        ]);
    }
}
