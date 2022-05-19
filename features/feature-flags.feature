Feature: Feature flags

Scenario: feature flags are attached to unhandled errors
  Given I set environment variable "BUGSNAG_FEATURE_FLAGS" to "[{ name: from config 1, variant: 1234 }, { name: from config 2 }]"
  And I start the symfony fixture
  When I navigate to the route "/feature-flags/unhandled"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And event 0 is unhandled
  And the event contains the following feature flags:
    | featureFlag            | variant    |
    | from config 1          | 1234       |
    | from config 2          |            |
    | added at runtime 1     |            |
    | added at runtime 2     | runtime_2  |
    | added at runtime 4     |            |
    | from global on error 1 | on error 1 |
    | from global on error 2 |            |
    | from global on error 3 | 111        |

Scenario: feature flags are attached to handled errors
  Given I set environment variable "BUGSNAG_FEATURE_FLAGS" to "[{ name: from config 1, variant: 1234 }, { name: from config 2 }]"
  And I start the symfony fixture
  When I navigate to the route "/feature-flags/handled"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And event 0 is handled
  And the event contains the following feature flags:
    | featureFlag            | variant        |
    | from config 1          | 1234           |
    | from config 2          |                |
    | added at runtime 1     |                |
    | added at runtime 2     | runtime_2      |
    | added at runtime 4     |                |
    | from global on error 1 | on error 1     |
    | from global on error 3 | 111            |
    | from notify on error   | notify 7636390 |

Scenario: feature flags can be cleared entirely with an unhandled error
  Given I set environment variable "BUGSNAG_FEATURE_FLAGS" to "[{ name: from config 1, variant: 1234 }, { name: from config 2 }]"
  And I start the symfony fixture
  When I navigate to the route "/feature-flags/unhandled?clear_all_flags"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And event 0 is unhandled
  And the event has no feature flags

Scenario: feature flags can be cleared entirely with a handled error
  Given I set environment variable "BUGSNAG_FEATURE_FLAGS" to "[{ name: from config 1, variant: 1234 }, { name: from config 2 }]"
  And I start the symfony fixture
  When I navigate to the route "/feature-flags/handled?clear_all_flags"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And event 0 is handled
  And the event has no feature flags
