<?php

namespace Drupal\wmsettings\Service;

use Drupal\Core\Config\Config;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\link\Plugin\Field\FieldType\LinkItem;

class WmSettings
{
    /** @var EntityTypeBundleInfoInterface */
    protected $entityTypeBundleInfo;
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var EntityRepositoryInterface */
    protected $entityRepository;
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var ImmutableConfig */
    protected $config;
    /** @var Config */
    protected $configEditable;
    /** @var array */
    protected $cache = []; // todo: replace with MemoryCacheInterface

    public function __construct(
        EntityTypeBundleInfoInterface $entityTypeBundleInfo,
        EntityTypeManagerInterface $entityTypeManager,
        EntityRepositoryInterface $entityRepository,
        LanguageManagerInterface $languageManager,
        ConfigFactoryInterface $configFactory
    ) {
        $this->entityTypeBundleInfo = $entityTypeBundleInfo;
        $this->entityTypeManager = $entityTypeManager;
        $this->entityRepository = $entityRepository;
        $this->languageManager = $languageManager;
        $this->config = $configFactory->get('wmsettings.settings');
        $this->configEditable = $configFactory->getEditable('wmsettings.settings');
    }

    public function getEntityType()
    {
        return 'settings';
    }

    /** Get all bundles for our entity type. */
    public function getAllBundles()
    {
        return $this->entityTypeBundleInfo->getBundleInfo($this->getEntityType());
    }

    /** Shortcut to the config. */
    public function readKeys()
    {
        return $this->config->get('keys');
    }

    /** Shortcut to the config. */
    public function updateKeys($keys)
    {
        // Check our instances every time we do this.
        $this->configEditable->set('keys', $keys)->save();
        $this->checkAndCreateEntities();
    }

    /** Get the values of a single setting. */
    public function readKey($key)
    {
        $keys = $this->readKeys();

        if (!empty($keys[$key])) {
            return $keys[$key];
        }

        return false;
    }

    /** Deletes a key. */
    public function deleteKey($key)
    {
        $keys = $this->readKeys();

        if (array_key_exists($key, $keys)) {
            unset($keys[$key]);
        }

        $this->updateKeys($keys);
    }

    /**
     * Write a single setting.
     *
     * $values = [
     *     'key' => 'x',
     *     'label' => 'x',
     *     'bundle' => 'x',
     * ]
     */
    public function updateKey($values)
    {
        $keys = $this->readKeys();

        $keys[$values['key']] = [
            'key' => $values['key'],
            'label' => $values['label'],
            'bundle' => $values['bundle'],
            'desc' => $values['desc'],
        ];

        $this->updateKeys($keys);
    }

    /**
     * This functions checks that for each key there is a corresponding
     * entity in the given bundle, and creates one if it's not there.
     */
    public function checkAndCreateEntities()
    {
        $storage = $this
            ->entityTypeManager
            ->getStorage($this->getEntityType());

        foreach ((array) $this->readKeys() as $value) {
            // Create an entity query for our entity type.
            $query = $storage
                ->getQuery()
                ->condition('wmsettings_key', $value['key'])
                ->condition('type', $value['bundle']);

            // Return the entities.
            $entities = $query->execute();

            if (empty($entities)) {
                $entity = $storage->create([
                    'type' => $value['bundle'],
                    'wmsettings_key' => $value['key'],
                ]);

                $entity->save();
                $entities[] = $entity;
            } else {
                $entities = $storage->loadMultiple($entities);
            }

            // Make sure translations, if available, are always published
            /** @var ContentEntityInterface $entity */
            foreach ($entities as $entity) {
                if (!$entity->hasField('content_translation_status')) {
                    continue;
                }

                foreach ($entity->getTranslationLanguages() as $language) {
                    $entity->getTranslation($language->getId())
                        ->set('content_translation_status', 1)
                        ->save();
                }
            }
        }
    }

    /**
     * Get the all, or get them by key.
     * @return ContentEntityInterface[]|ContentEntityInterface|false
     */
    public function read($key = null)
    {
        $currentLanguage = $this->languageManager->getCurrentLanguage();
        $cid = sprintf(
            '%s:%s',
            isset($key) ? $key : 'all',
            $currentLanguage->getId()
        );
        if (isset($this->cache[$cid])) {
            return $this->cache[$cid];
        }
        $query = $this
            ->entityTypeManager
            ->getStorage($this->getEntityType())
            ->getQuery();

        if ($key != null) {
            $query = $query
                ->condition('wmsettings_key', $key);
        }

        $ids = $query->execute();

        // Load them, and get them in the correct (=current) language.
        $entities = $this->entityTypeManager
            ->getStorage($this->getEntityType())
            ->loadMultiple($ids);

        foreach ($entities as $k => $v) {
            if ($v->hasTranslation($currentLanguage->getId())) {
                $v = $v->getTranslation($currentLanguage->getId());
            }

            $entities[$k] = $v;
        }

        // When a key is passed we are only interested in that one setting
        if ($key != null) {
            $entities = reset($entities);
        }

        return $this->cache[$cid] = $entities;
    }

    /** Shortcut to get data out of ordinary fields. */
    public function fill($entity, $fields)
    {
        $return = [];

        foreach ((array) $fields as $field_name => $type) {
            if ($entity->get($field_name) && !$entity->get($field_name)->isEmpty()) {
                switch ($type) {
                    case 'textarea':
                        $value = $entity->get($field_name)->first()->getValue();
                        // TODO: Dep inject renderer here.
                        $return[$field_name] = check_markup(
                            $value['value'],
                            $value['format']
                        );
                        break;
                    case 'textfield':
                        $return[$field_name] = $entity->get($field_name)->getString();
                        break;
                    case 'link':
                        /** @var LinkItem $linkItem */
                        $linkItem = $entity->get($field_name)->get(0);
                        $return[$field_name] = [
                            'url' => $linkItem->getUrl()->toString(),
                            'title' => $linkItem->title,
                        ];
                        break;
                    default:
                        $return[$field_name] = 'Unknown handler ' . $type . ' in WmSettings.php, line 254';
                        break;
                }
            } else {
                $return[$field_name] = '';
            }
        }

        return $return;
    }
}
