<?php

/**
 * @file
 * Contains \Drupal\wmsettings\Form\DeleteForm
 */
namespace Drupal\wmsettings\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\wmsettings\Service\WmSettings;

/**
 * Configure WmSettings settings for this site.
 */
class DeleteForm extends ConfirmFormBase
{
    /**
    * The settings.
    */
    protected $wmSettings;

    /**
     * Constructs a new SettingsForm object.
     *
     * @param \Drupal\Drupal\WmSettings\WmSettings $wm_settings
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
     * {@inheritdoc}
     */
    public function getFormId()
    {
        return 'wmsettings_settings_delete';
    }

    /**
     * {@inheritdoc}
     */
    public function getQuestion()
    {
        return t('Do you want to delete %id?', array('%id' => $this->id));
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelUrl()
    {
        return new Url('my_module.myroute');
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return t('Only do this if you are sure!');
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmText()
    {
        return t('Delete it!');
    }

    /**
     * {@inheritdoc}
     */
    public function getCancelText()
    {
        return t('Nevermind');
    }

    /**
     * {@inheritdoc}
     *
     * @param int $id
     *   (optional) The ID of the item to be deleted.
     */
    public function buildForm(array $form, FormStateInterface $form_state, $key = null)
    {
        $this->id = $key;
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $setting = $this->wmSettings->deleteKey($this->id);
    }
}
