Feature: Handled exceptions in controllers

Scenario: Handled exceptions are delivered from controllers
  Given I start the symfony fixture
  When I navigate to the route "/handled/controller/exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "LogicException"
  And the exception "message" equals "This is a handled exception"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/handled/controller/exception"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled/controller/exception"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

Scenario: Handled errors are delivered from controllers
  Given I start the symfony fixture
  When I navigate to the route "/handled/controller/error"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "Handled error"
  And the exception "message" equals "This is a handled error"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/handled/controller/error"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /handled/controller/error"
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledError"
