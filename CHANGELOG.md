# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## [0.7.5] - 2021-08-24
### Fixed
- Fix Drupal 9.2 Deprecation message ( https://www.drupal.org/node/3201242 )

## [0.7.4] - 2021-08-24
### Added
- Add a static cache to the `WmSettings` service

## [0.7.3] - 2021-06-17
### Changed
- Redirect to settings overview after saving, when having navigated to the settings edit form through the menu items

### Fixed
- Fix issue with form display of new settings bundles 

## [0.7.2] - 2021-04-02
### Fixed
- Fix error when no settings have been set up

## [0.7.1] - 2020-12-11
### Added
- Add static cache to Twig extension to prevent unnecessary database calls when calling the function multiple times with the same arguments

## [0.7.0] - 2020-09-21
### Added
- Add individual settings as menu items under the _Content_ > _Settings_ menu item

## [0.6.1] - 2020-09-21
### Added
- Fix Twig extension service definition

## [0.6.0] - 2020-09-09
### Added
- Add Twig extension

## [0.5.0] - 2020-07-23
### Removed
- Remove hook_event_dispatcher dependency

## [0.4.4] - 2020-05-23
### Fixed
- Fix notice 'Trying to access array offset on value of type bool'

## [0.4.3] - 2020-04-30
### Fixed
- Make entity type requirement optional during site installation with
 existing config

## [0.4.2] - 2020-03-16
### Fixed
- Fix issue with hook_requirements
- Make entity type requirement optional during installation

## [0.4.1] - 2020-03-09
### Fixed
- Fix invalid core_version_requirement parameter

## [0.4.0] - 2020-02-28
### Added
- Add changelog
- Add issue & pull request templates
- Add coding standard fixers
- Add Drupal 9 support

### Changed
- Increase PHP version constraint to 7.1
- Apply code style related fixes
- Update .gitignore
- Update module description
- Update README & documentation

### Fixed
- Fix automatic installation of base field
- Add missing hook_event_dispatcher dependency
- Add missing license

### Removed
- Remove unnecessary eck dependency

## [0.3.3] - 2019-12-12
### Added
- Add index on wmsettings_key base field

## [0.3.2] - 2019-10-17
### Changed
- Normalize composer.json
- Add php & drupal/core composer dependencies
- Replace usages of third-party deprecated code

## [0.3.1] - 2019-06-07
### Fixed
- Fix loading translations of settings entities

## [0.3.0] - 2019-03-15
### Added
- Dispatch cache tags on settings update/insert

## [0.2.5] - 2019-02-06
### Changed
- Remove references to deprecated classes
- Code style fixes

## [0.2.4] - 2019-11-22
### Changed
- Make sure translations of settings entities, if available, are always
  published

## [0.2.3] - 2019-03-30
### Changed
- Change entity type/bundle config to optional

## [0.2.2] - 2019-06-19
### Removed
- Remove Twig extension
