# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.2.0 - 2019-12-27

### Added

- [#23](https://github.com/zendframework/zend-config-aggregator/pull/23) adds the ability to specify the file mode for the generated cache file, when generating a cache file. The mode can be provided via the `Zend\ConfigAggregator\ConfigAggregator::CACHE_FILEMODE` configuration option. Modes should be expressed as octal values (e.g., `0600`).

- [#21](https://github.com/zendframework/zend-config-aggregator/pull/21) adds support for PHP 7.3.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.1.1 - 2018-04-04

### Added

- [#18](https://github.com/zendframework/zend-config-aggregator/pull/18) adds
  a package suggestion for [zend-config-aggregator-modulemanager](https://github.com/zendframework/zend-config-aggregator-modulemanager),
  which supplies a custom provider for using zend-mvc `Module` classes as
  configuration providers.

- [#19](https://github.com/zendframework/zend-config-aggregator/pull/19) adds
  a package suggestion for [zend-config-aggregator-parameters](https://github.com/zendframework/zend-config-aggregator-parameters),
  which supplies a post-processor consuming the Symfony DI `ParameterBag` in
  order to allow templated configuration parameter values.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.1.0 - 2018-02-26

### Added

- [#12](https://github.com/zendframework/zend-config-aggregator/pull/12) adds
  post-processing capabilities. Post processors are PHP callables (or class
  names of classes that define `__invoke()`) that accept the configuration
  array, and return a processed configuration array. Like providers, they are
  provided as an array of values when defining the `ConfigAggregator` instance,
  and passed as the third constructor argument. One use case is to re-use values
  by referencing another configuration key, ala the Symfony DI ParameterBag.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.0.1 - 2017-11-06

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [#14](https://github.com/zendframework/zend-config-aggregator/pull/14) fixes
  how file globbing of configuration files works on FreeBSD and IBM i Series
  systems. It does so by adding a dependency on zend-stdlib, which provides a
  glob compatibility layer.

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
