Feature: Handled exceptions in views

Scenario: Handled exceptions are delivered from views
  Given I start the symfony fixture
  When I navigate to the route "/handled/view/exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "LogicException"
  And the exception "message" equals "Handled exception"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/handled/view/exception"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled/view/exception"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

Scenario: Handled errors are delivered from views
  Given I start the symfony fixture
  When I navigate to the route "/handled/view/error"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "A handled error"
  And the exception "message" equals "handled"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/handled/view/error"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled/view/error"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledError"
