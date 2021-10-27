Feature: Discard classes

Scenario: Exceptions can be discarded by name
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "['RuntimeException']"
  And I start the symfony fixture
  When I navigate to the route "/discard-classes"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" matches "^App(Bundle)?\\Exception\\CustomException$"
  And the exception "message" equals "This is a CustomException"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/discard-classes"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /discard-classes"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"

Scenario: Exceptions can be discarded by regex
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "['/^App(Bundle)?\\Exception\\/']"
  And I start the symfony fixture
  When I navigate to the route "/discard-classes"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals 'This is a RuntimeException'
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/discard-classes"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /discard-classes"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

Scenario: Exceptions will be delivered when discard classes does not match
  Given I set environment variable "BUGSNAG_DISCARD_CLASSES" to "['Exception', '/^Logic/']"
  And I start the symfony fixture
  When I navigate to the route "/discard-classes"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the error payload field "events" is an array with 2 elements
  # the handled RuntimeException happens first so should be event 0
  And the error payload field "events.0.exceptions.0.errorClass" equals "RuntimeException"
  And the error payload field "events.0.exceptions.0.message" equals "This is a RuntimeException"
  And the error payload field "events.0.metaData.request.httpMethod" equals "GET"
  And the error payload field "events.0.metaData.request.url" ends with "/discard-classes"
  And the error payload field "events.0.app.type" equals "HTTP"
  And the error payload field "events.0.context" equals "GET /discard-classes"
  And the error payload field "events.0.severity" equals "warning"
  And the error payload field "events.0.unhandled" is false
  And the error payload field "events.0.severityReason.type" equals "handledException"
  # the unhandled CustomException should be event 1
  And the error payload field "events.1.exceptions.0.errorClass" matches the regex "^App(Bundle)?\\Exception\\CustomException$"
  And the error payload field "events.1.exceptions.0.message" equals "This is a CustomException"
  And the error payload field "events.1.metaData.request.httpMethod" equals "GET"
  And the error payload field "events.1.metaData.request.url" ends with "/discard-classes"
  And the error payload field "events.1.app.type" equals "HTTP"
  And the error payload field "events.1.context" equals "GET /discard-classes"
  And the error payload field "events.1.severity" equals "error"
  And the error payload field "events.1.unhandled" is true
  And the error payload field "events.1.severityReason.type" equals "unhandledExceptionMiddleware"
  And the error payload field "events.1.severityReason.attributes.framework" equals "Symfony"
