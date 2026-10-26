<?php

namespace Drupal\wmsettings\Install\Requirements;

use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Extension\InstallRequirementsInterface;
use Drupal\Core\Extension\Requirement\RequirementSeverity;

/**
 * Install time requirements, on Drupal 11.2 and later.
 *
 * Core loads this class before the module is installed, so it can't use any
 * other code from this module. WmSettingsRequirementsHooks reuses it for the
 * runtime and update requirements. Older versions of Drupal use
 * wmsettings_requirements() instead.
 */
class WmSettingsRequirements implements InstallRequirementsInterface
{
    public static function getRequirements(): array
    {
        $requirements = [];

        try {
            \Drupal::entityDefinitionUpdateManager()
                ->getEntityType('settings');
        } catch (PluginNotFoundException $e) {
            $requirements['settings_entity_type'] = [
                'title' => t('Settings entity type'),
                'value' => t('Missing <code>settings</code> entity type'),
                'description' => t('The wmsettings module needs a <code>settings</code> entity type. You can use the <a href="@eckModuleUrl">eck module</a> or you can create a custom entity type.', [
                    '@eckModuleUrl' => 'https://www.drupal.org/project/eck',
                ]),
                'severity' => RequirementSeverity::Warning,
            ];
        }

        return $requirements;
    }
}
