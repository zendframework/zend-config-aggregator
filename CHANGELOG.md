# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 0.4.0 - TBD

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- config cache file name defaults to NULL now, in order to avoid potentially unwanted disk access.
    Setting it to NULL will disable cache, even if `config_cache_enabled` is set to TRUE.

### Fixed

- Nothing.


## 0.3.1 - 24-12-2016

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Fixed required PHP version in composer.json file.

## 0.3.0 - 24-12-2016
  
### Added
  
- Added new constant, ConfigManager::ENABLE_CACHE for more verbose syntax when enabling cache: 
  `return [ConfigManager::ENABLE_CACHE => true];`.
  
### Deprecated
  
- Nothing.
  
### Removed

- #10 removes the dependency on zend-stdlib.
  
### Fixed

- Use "require" instead of "include" for loading cached configuration.


## 0.2.0 - 19-12-2015

### BC breaks

- Renamed GlobFileProvider to PhpFileProvider. While not desired, this could happen because
  ConfigManager is still in pre-release stage.
  
### Added
  
- Nothing.
  
### Deprecated
  
- Nothing.
  
### Removed
  
- Nothing.
  
### Fixed

- Nothing.

## 0.1.0

Initial prototype
