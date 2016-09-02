## WM Settings

Provides an Editor UI and Developers API for setting/getting content that needs to be all over the place but isn't exactly config, and isn't exactly entity stuff.

Technically you create config keys (eg `global` or `site-settings`). This key gets linked directly to exactly one entity in the `settings` entity type.

Developers are free to create bundles/fields in the entity type `settings` themselves, and create keys to refer to them.

### Adding configs

On `/admin/config/wmsettings` one can add settings. A setting effectively links a machine name to a specific entity id. The entity gets autocreated within the ECK ecosphere.

You can add configs to by directly writing config files I suppose, for the brave that feel up to it. Just follow the wmsettings.schema.yml for that.

### Editor UI

The administrative interface for editors is at `admin/content/wmsettings`. Note that:

* A separate permissions + eck settings edit permissions will be required to edit the settings
* Editors should only be able to edit these autocreated entities. Do NOT provide direct access to creation/deletion of settings entitites.

### Developers API

There are several ways to access the entity for a key.

#### 1. The service.

    $entity = Drupal::service('wmsettings.settings')
        ->read('my_key');

#### 2. The module function.

    wmsettings_get('my_key');
