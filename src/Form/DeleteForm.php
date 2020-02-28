<?php

namespace Drupal\wmsettings\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\wmsettings\Service\WmSettings;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeleteForm extends ConfirmFormBase
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
        return 'wmsettings_settings_delete';
    }

    public function getQuestion()
    {
        return $this->t('Do you want to delete %id?', ['%id' => $this->id]);
    }

    public function getCancelUrl()
    {
        return Url::fromRoute('my_module.myroute');
    }

    public function getDescription()
    {
        return $this->t('Only do this if you are sure!');
    }

    public function getConfirmText()
    {
        return $this->t('Delete it!');
    }

    public function getCancelText()
    {
        return $this->t('Nevermind');
    }

    public function buildForm(array $form, FormStateInterface $form_state, $key = null)
    {
        $this->id = $key;
        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->wmSettings->deleteKey($this->id);
    }
}
