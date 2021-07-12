Feature: Handled exceptions in commands

Scenario: Handled exceptions are delivered from commands
  Given I run the "app:exception:handled" command in the symfony fixture
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "LogicException"
  And the exception "message" equals "This is a handled exception in a command"
  And the error payload field "events.0.metaData" is an array with 0 elements
  And the event "app.type" equals "Console"
  And the event "context" is null
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledException"

Scenario: Handled errors are delivered from commands
  Given I run the "app:error:handled" command in the symfony fixture
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "Handled error"
  And the exception "message" equals "This is a handled error in a command"
  And the error payload field "events.0.metaData" is an array with 0 elements
  And the event "app.type" equals "Console"
  And the event "context" is null
  And the event "severity" equals "warning"
  And the event "unhandled" is false
  And the event "severityReason.type" equals "handledError"
