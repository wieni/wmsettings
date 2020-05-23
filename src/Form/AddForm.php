<?php

namespace Drupal\wmsettings\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\wmsettings\Service\WmSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AddForm extends ConfigFormBase
{
    /** @var WmSettings */
    protected $wmSettings;

    public static function create(ContainerInterface $container)
    {
        $instance = parent::create($container);
        $instance->wmSettings = $container->get('wmsettings.settings');

        return $instance;
    }

    public function getFormId()
    {
        return 'wmsettings_settings_add';
    }

    public function buildForm(array $form, FormStateInterface $form_state, $key = null)
    {
        $setting = $this->wmSettings->readKey($key);

        $form['label'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Label'),
            '#default_value' => $setting['label'] ?? '',
            '#size' => 30,
            '#required' => true,
            '#maxlength' => 64,
            '#description' => $this->t('The name for this setting.'),
        ];

        $form['key'] = [
            '#title' => $this->t('Key'),
            '#type' => 'machine_name',
            '#default_value' => $setting['key'] ?? '',
            '#maxlength' => 64,
            '#description' => $this->t('A unique name for this setting. It must only contain lowercase letters, numbers, and underscores.'),
            '#machine_name' => [
                'exists' => [$this, 'exists'],
            ],
            '#disabled' => !empty($setting['key']),
        ];

        $form['bundle'] = [
            '#type' => 'select',
            '#required' => true,
            '#title' => $this->t('Bundle'),
            '#options' => array_map(
                static function (array $info) {
                    return $info['label'];
                },
                $this->wmSettings->getAllBundles()
            ),
            '#default_value' => $setting['bundle'] ?? '',
        ];

        $form['desc'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Description'),
            '#default_value' => $setting['desc'] ?? '',
            '#size' => 128,
            '#required' => true,
            '#maxlength' => 255,
            '#description' => $this->t('The end-user description for this setting.'),
        ];

        return parent::buildForm($form, $form_state);
    }

    public function exists($key): bool
    {
        $setting = $this->wmSettings->readKey($key);

        return !empty($setting['key']);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->wmSettings->updateKey([
            'key' => $form_state->getValue('key'),
            'label' => $form_state->getValue('label'),
            'bundle' => $form_state->getValue('bundle'),
            'desc' => $form_state->getValue('desc'),
        ]);

        parent::submitForm($form, $form_state);
    }

    protected function getEditableConfigNames()
    {
        return [
            'wmsettings.settings.add',
        ];
    }
}
