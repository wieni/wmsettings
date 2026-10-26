<?php

namespace Drupal\wmsettings\Hook;

use Drupal\Core\Hook\Attribute\Hook;
use Drupal\wmsettings\Install\Requirements\WmSettingsRequirements;

/**
 * Runtime and update requirements, on Drupal 11.2 and later.
 *
 * Older versions of Drupal use wmsettings_requirements() instead.
 */
class WmSettingsRequirementsHooks
{
    #[Hook('runtime_requirements')]
    public function runtimeRequirements(): array
    {
        return WmSettingsRequirements::getRequirements();
    }

    #[Hook('update_requirements')]
    public function updateRequirements(): array
    {
        return WmSettingsRequirements::getRequirements();
    }
}
