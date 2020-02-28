<?php

namespace Drupal\wmsettings\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Url;
use Drupal\wmsettings\Service\WmSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SettingsOverview extends ControllerBase
{
    /** @var MessengerInterface */
    protected $messenger;
    /** @var WmSettings */
    protected $wmSettings;
    /** @var ModuleHandlerInterface */
    protected $moduleHandler;

    public static function create(ContainerInterface $container)
    {
        $instance = new static;
        $instance->messenger = $container->get('messenger');
        $instance->wmSettings = $container->get('wmsettings.settings');
        $instance->moduleHandler = $container->get('module_handler');

        return $instance;
    }

    /** Returns the admin screen with all keys. */
    public function overviewConfig(): array
    {
        $rows = [];

        foreach ((array) $this->wmSettings->readKeys() as $key => $value) {
            $operations = [
                'data' => [
                    '#type' => 'operations',
                    '#links' => [
                        'edit' => [
                            'url' => Url::fromRoute(
                                'wmsettings.settings.add',
                                [
                                    'key' => $key,
                                ],
                                [
                                    'query' => [
                                        'destination' => Url::fromRoute(
                                            'wmsettings.settings'
                                        )->toString(),
                                    ],
                                ]
                            ),
                            'title' => $this->t('Edit'),
                            ],
                        'delete' => [
                            'url' => Url::fromRoute(
                                'wmsettings.settings.delete',
                                [
                                    'key' => $key,
                                ],
                                [
                                    'query' => [
                                        'destination' => Url::fromRoute(
                                            'wmsettings.settings'
                                        )->toString(),
                                    ],
                                ]
                            ),
                            'title' => $this->t('Delete'),
                        ],
                    ],
                ],
            ];

            $rows[] = [
                $key,
                $value['label'],
                $value['desc'],
                $operations,
            ];
        }

        return [
            '#theme' => 'table',
            '#rows' => $rows,
            '#header' => [
                $this->t('Key'),
                $this->t('Label'),
                $this->t('Description'),
                $this->t('Operations'),
            ],
        ];
    }

    /** Return the overview for editors. */
    public function overviewContent(): array
    {
        $keys = $this->wmSettings->readKeys();

        $rows = [];

        // Get all content.
        foreach ((array) $this->wmSettings->read() as $key => $value) {
            $operations = [
                'data' => [
                    '#type' => 'operations',
                    '#links' => [],
                ],
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
                                'wmsettings.content'
                            )->toString(),
                        ],
                    ]
                ),
                'title' => $this->t('Edit'),
            ];
            $operations['data']['#links']['edit'] = $editOperation;

            if ($this->moduleHandler->moduleExists('content_translation')) {
                $translateOperation = [
                    'url' => Url::fromRoute(
                        'entity.' . $this->wmSettings->getEntityType() . '.content_translation_overview',
                        [
                            'settings' => $value->id(),
                        ],
                        [
                            'query' => [
                                'destination' => Url::fromRoute(
                                    'wmsettings.content'
                                )->toString(),
                            ],
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

        return [
            '#theme' => 'table',
            '#rows' => $rows,
            '#empty' => $this->t('No settings found'),
            '#header' => [
                $this->t('Label'),
                $this->t('Description'),
                $this->t('Operations'),
            ],
        ];
    }

    /** Redirect to the correct setting (and tab). */
    public function redirectSetting($key, $destination, $anchor): RedirectResponse
    {
        $setting = $this->wmSettings->readKey($key);

        // Redirect raw when we can't find the key.
        if (!$setting) {
            $this->messenger->addStatus(
                t('Unknown wmSettings key: %key', ['%key' => $key]),
                'error'
            );

            return $this->redirect($destination);
        }

        $settingData = $this->wmSettings->read($key);

        return $this->redirect(
            'entity.settings.edit_form',
            [
                'settings' => $settingData->id(),
            ],
            [
                'fragment' => $anchor,
                'query' => [
                    'destination' => Url::fromRoute($destination)->toString(),
                ],
            ]
        );
    }
}
