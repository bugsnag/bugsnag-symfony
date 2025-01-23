Feature: A custom Guzzle client can be used

Scenario: Custom Guzzle client is not used when config is not setup
  Given I start the symfony fixture
  When I navigate to the route "/unhandled/controller/exception"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the error "X-Custom-Guzzle" header is not present

Scenario: A custom Guzzle client can be used
  Given I set environment variable "BUGSNAG_GUZZLE" to "custom_guzzle"
  And I start the symfony fixture
  When I navigate to the route "/unhandled/controller/exception"
  And I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the error "X-Custom-Guzzle" header equals "yes"
