wmsettings
======================

[![Latest Stable Version](https://poser.pugx.org/wieni/wmsettings/v/stable)](https://packagist.org/packages/wieni/wmsettings)
[![Total Downloads](https://poser.pugx.org/wieni/wmsettings/downloads)](https://packagist.org/packages/wieni/wmsettings)
[![License](https://poser.pugx.org/wieni/wmsettings/license)](https://packagist.org/packages/wieni/wmsettings)

> Provides an Editor UI and Developer API for managing custom settings

## Why?
Provides an Editor UI and Developers API for setting/getting content that needs to be all over the place but isn't exactly config, and isn't exactly entity stuff.

## How does it work?
Technically you create config keys (eg `global` or `site-settings`). This key gets linked directly to exactly one bundle in the `settings` entity type.

Developers are free to create bundles/fields in the entity type `settings` themselves, and create keys to refer to them.

### Adding configs
On `/admin/config/wmsettings` one can add settings. A setting effectively links a machine name to a specific entity id. The entity gets autocreated.

You can also add configs by directly writing config files I suppose, for the brave that feel up to it. Just follow the wmsettings.schema.yml for that.

### Editor UI
The administrative interface for editors is at `admin/content/wmsettings`. Note that:

* The `administer wmsettings content` permission + edit permissions for the `settings` entity type will be required to edit the settings
* Editors should only be able to edit these autocreated entities. Do NOT provide direct access to creation/deletion of settings entitites.

### Developers API
There are several ways to access the entity for a key.

#### 1. The service.

```php
$entity = Drupal::service('wmsettings.settings')
    ->read('my_key');
```

#### 2. The module function.

```php
wmsettings_get('my_key');
```

#### 3. The fill function.

Returns a quick flattened array of all values in a setting, given field names and field types. Not a lot of field types are supported now.

```php
$variables['copy'] = Drupal::service('wmsettings.settings')
    ->fill($global_copy, ['field_name' => 'textarea']);
```

### Adding Tabs (local tasks) to settings for your custom routes
Drupal requires at least two tasks to show them, so for the sake of completeness this example loads two tasks too:

```yaml
wmcustom.home_controller_home.home:
    title: 'View'
    route_name: wmcustom.home_controller_home
    base_route: wmcustom.home_controller_home
  
wmcustom.home_controller_home.edit:
    title: 'Edit'
    route_name: wmsettings.settings.redirect
    route_parameters:
        key:  'homepage'
        anchor: 'edit-group-hero-image'
        destination: 'wmcustom.home_controller_home'
    base_route: wmcustom.home_controller_home
```

## Changelog
All notable changes to this project will be documented in the
[CHANGELOG](CHANGELOG.md) file.

## Security
If you discover any security-related issues, please email
[security@wieni.be](mailto:security@wieni.be) instead of using the issue
tracker.

## License
Distributed under the MIT License. See the [LICENSE](LICENSE) file
for more information.
