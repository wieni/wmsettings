<?php

namespace Drupal\wmsettings\Service;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Provides common functionality for content translation.
 */
class WmSettings
{

    /**
     * The entity manager.
     *
     * @var \Drupal\Core\Entity\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * The entity type manager.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * The query interface.
     */
    protected $entityQuery;

    /**
     * The language manager.
     */
    protected $languageManager;

    /**
     * The config.
     *
     * @var \Drupal\Core\Config\ImmutableConfig
     */
    protected $config;

    /**
     * The config, editable.
     *
     * @var \Drupal\Core\Config\ImmutableConfig
     */
    protected $config_editable;

    /**
     * Constructs a WmContentManageAccessCheck object.
     *
     * @param \Drupal\Core\Entity\EntityManagerInterface $manager
     *   The entity type manager.
     * @param \Drupal\Core\Entity\QueryFactory $query
     *   The query factory.
     * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
     *   The language manager.
     * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
     *   The factory for configuration objects.
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EntityTypeManagerInterface $entityTypeManager,
        QueryFactory $query,
        LanguageManagerInterface $language_manager,
        ConfigFactoryInterface $config_factory
    ) {
        $this->entityManager = $entityManager;
        $this->entityTypeManager = $entityTypeManager;
        $this->entityQuery = $query;
        $this->languageManager = $language_manager;
        $this->config = $config_factory->get('wmsettings.settings');
        $this->config_editable = $config_factory->getEditable('wmsettings.settings');
    }

    public function getEntityType()
    {
        return 'settings';
    }

    /**
     * Get all bundles for our eck type.
     */
    public function getAllBundles()
    {
        return $this->entityManager->getBundleInfo($this->getEntityType());
    }

    /**
     * Shortcut to the config.
     */
    public function readKeys()
    {
        return $this->config->get('keys');
    }

    /**
     * Shortcut to the config.
     */
    public function updateKeys($keys)
    {
        // Check our instances every time we do this.
        $this->config_editable->set('keys', $keys)->save();
        $this->checkAndCreateEntities();
    }

    /**
     * Get the values of a single setting.
     */
    public function readKey($key)
    {
        $keys = $this->readKeys();

        if (!empty($keys[$key])) {
            return $keys[$key];
        }

        return false;
    }

    /**
     * Deletes a key.
     */
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
        foreach ((array)$this->readKeys() as $value) {
            // Create an entity query for our entity type.
            $query = $this
                ->entityQuery
                ->get($this->getEntityType())
                ->condition('wmsettings_key', $value['key'])
                ->condition('type', $value['bundle']);

            // Return the entities.
            $result = $query->execute();

            if (empty($result)) {
                $entity = $this
                    ->entityTypeManager
                    ->getStorage($this->getEntityType())
                    ->create([
                        'type' => $value['bundle'],
                        'wmsettings_key' => $value['key']
                    ])
                    ->save();
            }
        }
    }

    /**
     * Get the all, or get them by key.
     */
    public function read($key = null)
    {
        // Create an entity query for our entity type.
        $query = $this
            ->entityQuery
            ->get($this->getEntityType());

        if ($key != null) {
            $query = $query
                ->condition('wmsettings_key', $key);
        }

        $ids = $query->execute();

        // Return them loaded.
        $entities = $this->entityTypeManager
            ->getStorage($this->getEntityType())
            ->loadMultiple($ids);

        if ($key != null) {
            return reset($entities);
        }
        return $entities;
    }
}
