<?php

namespace Drupal\wmsettings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Url;

use Drupal\wmsettings\Service\WmSettings;

/**
 * Provides a list of wmsettings settings
 */
class SettingsOverview extends ControllerBase
{
    /**
     * The settings.
     *
     * @var \Drupal\wmsettings\WmSettings
     */
    protected $wmSettings;

    /**
     * @param \Drupal\wmsettings\WmSettings $wm_settings
     *   A wmcontent manager instance.
     */
    public function __construct(WmSettings $wm_settings)
    {
        $this->wmSettings = $wm_settings;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container)
    {
        return new static(
            $container->get('wmsettings.settings')
        );
    }

    /**
     * Returns the admin screen with all keys.
     */
    public function overviewConfig()
    {
        $rows = [];

        foreach ((array)$this->wmSettings->readKeys() as $key => $value) {
            $operations = [
                'data' => [
                    '#type' => 'operations',
                    '#links' => [
                        'edit' => [
                            'url' => Url::fromRoute(
                                "wmsettings.settings.add",
                                [
                                    'key' => $key,
                                ],
                                [
                                    'query' => [
                                        'destination' => Url::fromRoute(
                                            "wmsettings.settings"
                                        )->toString(),
                                    ]
                                ]
                            ),
                            'title' => $this->t('Edit'),
                            ],
                        'delete' => [
                            'url' => Url::fromRoute(
                                "wmsettings.settings.delete",
                                [
                                    'key' => $key,
                                ],
                                [
                                    'query' => [
                                        'destination' => Url::fromRoute(
                                            "wmsettings.settings"
                                        )->toString(),
                                    ]
                                ]
                            ),
                            'title' => $this->t('Delete'),
                        ],
                    ]
                ]
            ];


            $rows[] = [
                $key,
                $value['label'],
                $value['desc'],
                $operations,
            ];
        }

        $build = [
            '#theme' => 'table',
            '#rows' => $rows,
            '#header' => [
                $this->t('Key'),
                $this->t('Label'),
                $this->t('Description'),
                $this->t('Operations'),
            ]
        ];

        return $build;
    }

    /**
     * Return the overview for editors.
     */
    public function overviewContent()
    {
        $keys = $this->wmSettings->readKeys();

        $rows = [];

        // Get all content.
        foreach ((array)$this->wmSettings->read() as $key => $value) {
            $operations = [
                'data' => [
                    '#type' => 'operations',
                    '#links' => [],
                ]
            ];
            
            $editOperation = [
                'url' => Url::fromRoute(
                    'entity.' . $this->wmSettings->getEntityType() . '.edit_form',
                    [
                        'settings' => $value->id(),
                    ],
                    [
                        'query' => [
                            'destination' => Url::fromRoute(
                                "wmsettings.content"
                            )->toString(),
                        ]
                    ]
                ),
                'title' => $this->t('Edit'),
            ];
            $operations['data']['#links']['edit'] = $editOperation;
            
            if (\Drupal::moduleHandler()->moduleExists('content_translation'))
            {
                $translateOperation = [
                    'url' => Url::fromRoute(
                        'entity.' . $this->wmSettings->getEntityType() . '.content_translation_overview',
                        [
                            'settings' => $value->id(),
                        ],
                        [
                            'query' => [
                                'destination' => Url::fromRoute(
                                    "wmsettings.content"
                                )->toString(),
                            ]
                        ]
                    ),
                    'title' => $this->t('Translate'),
                ];
                $operations['data']['#links']['translate'] = $translateOperation;
            }


            if (isset($keys[$value->wmsettings_key->value])) {
                $rows[] = [
                    $keys[$value->wmsettings_key->value]['label'],
                    $keys[$value->wmsettings_key->value]['desc'],
                    $operations,
                ];
            }
        }

        $build = [
            '#theme' => 'table',
            '#rows' => $rows,
            '#empty' => $this->t('No settings found'),
            '#header' => [
                $this->t('Label'),
                $this->t('Description'),
                $this->t('Operations'),
            ]
        ];

        return $build;
    }
}
