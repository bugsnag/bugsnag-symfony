Feature: Runtime versions are reported accurately

Scenario: Handled exceptions report runtime versions
  Given I start the symfony fixture
  When I navigate to the route "/handled/controller/exception"
  Then I wait to receive an error
  And the exception "errorClass" equals "LogicException"
  And the exception "message" equals "This is a handled exception"
  And the event "device.runtimeVersions.php" matches the current PHP version
  And the event "device.runtimeVersions.symfony" matches the current major Symfony version

Scenario: Unhandled exceptions report runtime versions
  Given I start the symfony fixture
  When I navigate to the route "/unhandled/controller/exception"
  Then I wait to receive an error
  And the error is valid for the error reporting API version "4.0" for the "Bugsnag Symfony" notifier
  And the exception "errorClass" equals "RuntimeException"
  And the exception "message" equals "Crashing exception!"
  And the event "device.runtimeVersions.php" matches the current PHP version
  And the event "device.runtimeVersions.symfony" matches the current major Symfony version
