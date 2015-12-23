# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.


## 0.3.0 - TBD
  
### Added
  
- Added new constant, ConfigManager::ENABLE_CACHE for more verbose syntax when enabling cache: 
  `return [ConfigManager::ENABLE_CACHE => true];`.
  
### Deprecated
  
- Nothing.
  
### Removed
  
- Nothing.
  
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
