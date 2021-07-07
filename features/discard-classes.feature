Feature: Discard classes

Scenario: Exceptions can be discarded by name
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "['RuntimeException']"
  And I start the symfony fixture
  When I navigate to the route "/unhandled/controller/exception"
  Then I should receive no requests

Scenario: Exceptions can be discarded by regex
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "['/^Run.*ion$/']"
  And I start the symfony fixture
  When I navigate to the route "/unhandled/controller/exception"
  Then I should receive no requests

Scenario: Exceptions will be delivered when discard classes does not match
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "['Exception', '/^Logic/']"
  And I start the symfony fixture
  When I navigate to the route "/unhandled/controller/exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "Crashing exception!"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/unhandled/controller/exception"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled/controller/exception"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"
