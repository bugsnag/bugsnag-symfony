Feature: The current user ID should be detected automatically

Scenario: The user ID of 'abc' should be detected when they are signed in
  Given I start the symfony fixture
  When I navigate to the route "/unhandled/controller/exception?name=abc"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the event "user.id" equals "abc-123"
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "Crashing exception!"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/unhandled/controller/exception?name=abc"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled/controller/exception"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"

Scenario: The user ID of 'xyz' should be detected when they are signed in
  Given I start the symfony fixture
  When I navigate to the route "/unhandled/controller/exception?name=xyz"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the event "user.id" equals "xyz-789"
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "Crashing exception!"
  And the event "metaData.request.httpMethod" equals "GET"
  And the event "metaData.request.url" ends with "/unhandled/controller/exception?name=xyz"
  And the event "app.type" equals "HTTP"
  And the event "context" equals "GET /unhandled/controller/exception"
  And the event "severity" equals "error"
  And the event "unhandled" is true
  And the event "severityReason.type" equals "unhandledExceptionMiddleware"
  And the event "severityReason.attributes.framework" equals "Symfony"
