Feature: Out of memory error support

Scenario: OOM from a single large allocation
  Given I start the symfony fixture
  When I navigate to the route "/oom/big"
  Then the Symfony response matches "Allowed memory size of \d+ bytes exhausted \(tried to allocate \d+ bytes\)"
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "Symfony\Component\ErrorHandler\Error\OutOfMemoryError"
  And the exception "message" matches "Allowed memory size of \d+ bytes exhausted \(tried to allocate \d+ bytes\)"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /oom/big"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"

Scenario: OOM from many small allocations
  # Symfony does a lot more stuff in debug mode, which can cause it to run OOM
  # again when trying to handle the original OOM
  Given I set environment variable "APP_DEBUG" to "0"
  And I start the symfony fixture
  When I navigate to the route "/oom/small"
  Then the Symfony response matches "Allowed memory size of \d+ bytes exhausted \(tried to allocate \d+ bytes\)"
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "Symfony\Component\ErrorHandler\Error\OutOfMemoryError"
  And the exception "message" matches "Allowed memory size of \d+ bytes exhausted \(tried to allocate \d+ bytes\)"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /oom/small"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"
