# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.0.0 - 2017-04-24

### Added

- [#7](https://github.com/zendframework/zend-config-aggregator/pull/7) adds
  online documentation at https://docs.zendframework.com/zend-config-aggregator/

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.1 - 2017-04-23

### Added

- [#3](https://github.com/zendframework/zend-config-aggregator/pull/3) added
  zend-config ^3.0 support

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.2.0 - 2017-01-11

### Added

- [#2](https://github.com/zendframework/zend-config-aggregator/pull/2) adds a
  new `ArrayProvider`, which accepts an array to its constructor, and returns
  it when invoked. This can be used to provide in-line array configuration when
  feeding the `ConfigAggregator` instance.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 0.1.0 - 2016-12-08

Initial release.

Based on the 0.4.0 version of [mtymek/expressive-config-manager](https://github.com/mtymek/expressive-config-manager),
this version renames the namespace from `Zend\Expressive\ConfigManager` to
`Zend\ConfigAggregator`, and renames the `ConfigManager` class to
`ConfigAggregator`. All other functionality remains the same.
