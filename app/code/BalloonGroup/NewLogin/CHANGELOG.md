### Author:
Gilberto Aguirre <gilberto.aguirre@balloon-group.com> || Ballon Group Team

### Copyright:
Copyright (c) 2022 Ballon Group (https://www.balloon-group.com/)

### Package:
BalloonGroup_NewLogin

# Changelog
All notable changes to this module will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this module adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.6] - 2022-06-14
## Commit: #1173631 - Streamline the registration process VI - EVO (v5)
### Added
- Adding isdni condition validation in "aroundExecute" plugin in path "Plugin\Customer\Account\CreatePost.php".

## [1.0.5] - 2022-06-13
## Commit: #1173631 - Streamline the registration process VI - EVO (v4)
### Added
- Adding domain validation in "aroundExecute" and "afterExecute" plugin in path "Plugin\Customer\Account\CreatePost.php".

## [1.0.4] - 2022-06-09
## Commit: #1173631 - Streamline the registration process VI - EVO (v3)
### Fixed
- Fixing CHANGELOG.md.

## [1.0.3] - 2022-06-09
## Commit: #1173631 - Streamline the registration process VI - EVO (v2)
### Added
- Adding layout, template, and controller to print "customerAlreadyExistsErrorMessage" template in path "Plugin\Customer\Account\CreatePost.php".

## [1.0.2] - 2022-06-08
## Commit: #1173631 - Streamline the registration process VI - EVO
### Added
- Adding viewModel to path "view/frontend/templates/form/register.phtml" to add agreements checkbox.
- Adding Plugin in path "Plugin\Pricing\Render\FinalPriceBox.php" to hide pricing when users is guest.
- Adding Plugin in path "Plugin\Pricing\Render\FinalPriceBoxBundle.php" to hide pricing when users is guest.

## [1.0.1] - 2022-05-23
## Commit: #1266572 - Fix create account doesnt receive email
### Added
- Removing arounExecute which causes account creation to fail, and adding logic in the plugin's afterExecute method in the "Plugin/Customer/Account/CreatePost.php" path.