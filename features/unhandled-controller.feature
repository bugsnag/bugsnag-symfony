Feature: Unhandled exceptions in controllers

Scenario: Unhandled exceptions are delivered from controllers
  Given I start the symfony fixture
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

Scenario: Unhandled errors are delivered from controllers
  Given I start the symfony fixture
  When I navigate to the route "/unhandled/controller/error"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "Symfony\Component\ErrorHandler\Error\UndefinedFunctionError"
  And the exception "message" equals 'Attempted to call function \\"foo\\" from namespace \\"App\Controller\\".'
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/unhandled/controller/error"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled/controller/error"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"
