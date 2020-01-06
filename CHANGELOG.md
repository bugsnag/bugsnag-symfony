Changelog
=========

## 1.6.1 (2020-01-06)

### Fixes

* Fix potential issue with ExceptionEvent missing getThrowable method in Symfony 4.3.
  Additionally adds InvalidArgumentException in the case the event is triggered with an incorrect class.
  [Loïck Piera](https://github.com/pyrech)
  [#99](https://github.com/bugsnag/bugsnag-symfony/pull/99)


## 1.6.0 (2019-12-03)

### Enhancements

* Add Symfony-specific shutdown strategy
  [Richard Harrison](https://github.com/rjharrison)
  [#87](https://github.com/bugsnag/bugsnag-symfony/pull/87)

* Add Symfony-5 support including examples
  [Loïck Piera](https://github.com/pyrech)
  [#93](https://github.com/bugsnag/bugsnag-symfony/pull/93)

## 1.5.1 (2019-06-24)

### Bug Fixes

* Removed deprecation warnings on `Request.getSession` and `ConsoleEvents::EXCEPTION`
  [Jan Myszkier](https://github.com/janmyszkier)
  [#79](https://github.com/bugsnag/bugsnag-symfony/pull/79)
* Removed `TreeBuilder.root` deprecated usage
  [Damien Alexandre](https://github.com/damienalexandre)
  [#80](https://github.com/bugsnag/bugsnag-symfony/pull/80)

### Enhancements

* Add Symfony version string to report (device.runtimeVersions)
  [#78](https://github.com/bugsnag/bugsnag-symfony/pull/78)

## 1.5.0 (2018-02-01)

This release adds support for Symfony 4. A guide on integrating the notifier with a Symfony 4 application can be found in the [Bugsnag Symfony integration guide](https://docs.bugsnag.com/platforms/php/symfony/).

### Enhancements

* Added support for Symfony 4
  [#55](https://github.com/bugsnag/bugsnag-symfony/pull/55)
  [#62](https://github.com/bugsnag/bugsnag-symfony/pull/62)
  [#65](https://github.com/bugsnag/bugsnag-symfony/pull/65)

## 1.4.0 (2017-12-21)

### Enhancements

* Bumped version to Bugsnag-PHP 3.10.0 to add support for `addMetaData`

## 1.3.0 (2017-11-23)

### Enhancements

* Added callback examples in Symfony 31
  [#49](https://github.com/bugsnag/bugsnag-symfony/pull/49)
  [#52](https://github.com/bugsnag/bugsnag-symfony/pull/52)

## 1.2.0 (2017-10-02)

### Enhancements

* Added issue template
  [#42](https://github.com/bugsnag/bugsnag-symfony/pull/42)

* Added additional data for unhandled/handled feature
  [#45](https://github.com/bugsnag/bugsnag-symfony/pull/45)

## 1.1.0 (2017-06-28)

### Enhancements

* Added automatic user detection
  [Graham Campbell](https://github.com/GrahamCampbell)
  [#15](https://github.com/bugsnag/bugsnag-symfony/pull/15)

* Support setting release stage from config
  [Graham Campbell](https://github.com/GrahamCampbell)
  [#39](https://github.com/bugsnag/bugsnag-symfony/pull/39)

### Bug Fixes

* Fixed edge cases with automatic project root detection
  [Graham Campbell](https://github.com/GrahamCampbell)
  [#31](https://github.com/bugsnag/bugsnag-symfony/pull/31)

* Fixed bug with empty json content decoding
  [Graham Campbell](https://github.com/GrahamCampbell)
  [#33](https://github.com/bugsnag/bugsnag-symfony/pull/33)

## 1.0.0 (2016-09-05)

* First public release
