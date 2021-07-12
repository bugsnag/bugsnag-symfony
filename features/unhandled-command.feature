Feature: Unhandled exceptions in commands

Scenario: Unhandled exceptions are delivered from commands
  Given I run the "app:exception:unhandled" command in the symfony fixture
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "Crashing exception in a command!"
  And the event "metaData.command.status" equals 1
  And the event "metaData.command.name" equals "app:exception:unhandled"
  And the event "app.type" equals "Console"
  And the event "context" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"

Scenario: Unhandled errors are delivered from commands
  Given I run the "app:error:unhandled" command in the symfony fixture
  When I wait to receive an error
  Then the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "Error"
  And the exception "message" equals "Call to undefined function App\Command\foo()"
  And the event "metaData.command.status" equals 1
  And the event "metaData.command.name" equals "app:error:unhandled"
  And the event "app.type" equals "Console"
  And the event "context" is null
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"
