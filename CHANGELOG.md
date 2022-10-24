Changelog
=========

## 1.13.0 (2022-10-24)

### Enhancements

* Add `max_breadcrumbs` config option for configuring the maximum number of breadcrumbs to attach to a report
  [#158](https://github.com/bugsnag/bugsnag-symfony/pull/158)

## 1.12.0 (2022-05-20)

### Enhancements

* New APIs to support feature flag and experiment functionality. For more information, please see https://docs.bugsnag.com/product/features-experiments.
  [#153](https://github.com/bugsnag/bugsnag-symfony/pull/153)

## 1.11.2 (2022-02-02)

### Bug Fixes

* Fix PHP 8.1 deprecation from `stripos` in `Request::getInput`
  [phillylovepark](https://github.com/phillylovepark)
  [#147](https://github.com/bugsnag/bugsnag-symfony/pull/147)
  [#149](https://github.com/bugsnag/bugsnag-symfony/pull/149)

## 1.11.1 (2022-01-19)

### Bug Fixes

* Call `getUserIdentifier` instead of `getUsername` on Symfony 6
  [#145](https://github.com/bugsnag/bugsnag-symfony/pull/145)

## 1.11.0 (2021-12-13)

### Enhancements

* Add support for Symfony 6
  [Julien Cousin-Alliot](https://github.com/Nispeon)
  [#137](https://github.com/bugsnag/bugsnag-symfony/pull/137)

### Bug Fixes

* Prevent a deprecation from `BugsnagListener::getSubscribedEvents`
  [#138](https://github.com/bugsnag/bugsnag-symfony/pull/138)

## 1.10.0 (2021-06-30)

### Enhancements

* Add support for Symfony Messenger. Exceptions in workers will now automatically be reported to Bugsnag. The queue of events will also be flushed after each successful job
  [Mathieu](https://github.com/MatTheCat)
  [#89](https://github.com/bugsnag/bugsnag-symfony/pull/89)
  [#125](https://github.com/bugsnag/bugsnag-symfony/pull/125)

### Bug Fixes

* Use `hasPreviousSession` instead of `hasSession` when checking for session data
  [Oleg Andreyev](https://github.com/oleg-andreyev)
  [#124](https://github.com/bugsnag/bugsnag-symfony/pull/124)

* Set the severity of exceptions to "error" instead of "warning"
  [#126](https://github.com/bugsnag/bugsnag-symfony/pull/126)

## 1.9.0 (2021-02-10)

### Enhancements

* Out of memory errors will now be reported by increasing the memory limit by 5 MiB. Use the new `memoryLimitIncrease` configuration option to change the amount of memory, or set it to `null` to disable the increase entirely.
  [#119](https://github.com/bugsnag/bugsnag-symfony/pull/119)

* Support the new `discardClasses` configuration option. This allows events to be discarded based on the exception class name or PHP error name.
  [#120](https://github.com/bugsnag/bugsnag-symfony/pull/120)

* Support the new `redactedKeys` configuration option. This is similar to `filters` but allows both strings and regexes. String matching is exact but case-insensitive. Regex matching allows for partial and wildcard matching.
  [#121](https://github.com/bugsnag/bugsnag-symfony/pull/121)

### Deprecations

* The `filters` configuration option is now deprecated as `redactedKeys` can express everything that filters could and more.

## 1.8.0 (2020-11-25)

### Enhancements

* Allow passing a Guzzle instance to Bugsnag
  [#117](https://github.com/bugsnag/bugsnag-symfony/pull/117)

## 1.7.0 (2020-06-01)

### Enhancements

* Add `project_root_regex` and `strip_path_regex` options for using regexes to match the project root and strip path
  [#109](https://github.com/bugsnag/bugsnag-symfony/pull/109)

## 1.6.2 (2020-02-26)

### Bug Fixes

* Added support for PHP 7.4
  [#104](https://github.com/bugsnag/bugsnag-symfony/pull/104)

## 1.6.1 (2020-01-06)

### Bug Fixes

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

### Bug Fixes

* Added support for PHP 7.2 and 7.3
  [#87](https://github.com/bugsnag/bugsnag-symfony/pull/87)

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
