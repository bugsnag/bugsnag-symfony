Feature: Unhandled exceptions in views

Scenario: Unhandled exceptions are delivered from views
  Given I start the symfony fixture
  When I navigate to the route "/unhandled/view/exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "Twig\Error\RuntimeError"
  And the exception "message" equals 'An exception has been thrown during the rendering of a template (\\"Crash!\\").'
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/unhandled/view/exception"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled/view/exception"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"

Scenario: Unhandled errors are delivered from views
  Given I start the symfony fixture
  When I navigate to the route "/unhandled/view/error"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "Symfony\Component\ErrorHandler\Error\UndefinedFunctionError"
  And the exception "message" equals 'Attempted to call function \\"abcxyz\\" from namespace \\"App\Twig\\".'
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/unhandled/view/error"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled/view/error"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"
